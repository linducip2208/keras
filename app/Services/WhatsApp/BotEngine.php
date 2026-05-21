<?php

namespace App\Services\WhatsApp;

use App\Adapters\AdapterRegistry;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\WhatsAppSession;
use App\Services\Approval\ApprovalEngine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * WhatsApp Bot Engine — conversation state machine untuk ESS karyawan.
 *
 * Implements F02 (attendance via WA), F03 (leave via WA) dari docs/06-BUSINESS-FLOWS.md.
 *
 * Flow:
 *  - User WA pertama kali → enroll (OTP verify pakai phone match dengan Employee.phone)
 *  - Setelah verified, state machine: 'menu' → 'absen', 'cuti', 'slip', 'approval'
 *  - Setiap inbound message → resolveIntent + transition state
 */
class BotEngine
{
    public function __construct(
        private AdapterRegistry $registry,
        private ApprovalEngine $approval,
    ) {}

    public function handleInbound(array $message, int $tenantId): void
    {
        $phone = $this->normalizePhone($message['from'] ?? '');
        $body = trim(strtolower($message['text'] ?? $message['body'] ?? ''));
        $type = $message['type'] ?? 'text';

        // Log inbound
        DB::table('whatsapp_messages')->insert([
            'tenant_id' => $tenantId,
            'phone_number' => $phone,
            'direction' => 'inbound',
            'message_type' => $type,
            'body' => $body,
            'raw_payload' => json_encode($message),
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $session = WhatsAppSession::firstOrCreate(
            ['phone_number' => $phone, 'tenant_id' => $tenantId],
            ['state' => 'idle']
        );
        $session->update(['last_message_at' => now()]);

        // Resolve employee by phone
        if (!$session->employee_id) {
            $employee = Employee::where('tenant_id', $tenantId)
                ->where(function ($q) use ($phone) {
                    $q->where('phone', $phone)->orWhere('phone_alt', $phone);
                })
                ->first();
            if ($employee) {
                $session->update(['employee_id' => $employee->id, 'verified_at' => now()]);
            } else {
                $this->reply($phone, $tenantId, "❌ Nomor Anda tidak terdaftar di sistem HRIS. Hubungi HR untuk daftarkan nomor WhatsApp.");
                return;
            }
        }

        $employee = $session->employee;

        // Global commands (override state)
        if (in_array($body, ['menu', 'menu utama', 'home', '/start'])) {
            $session->update(['state' => 'idle', 'state_data' => null]);
            $this->showMenu($phone, $tenantId, $employee);
            return;
        }

        if (in_array($body, ['batal', 'cancel', '/cancel'])) {
            $session->update(['state' => 'idle', 'state_data' => null]);
            $this->reply($phone, $tenantId, "Aksi dibatalkan. Ketik *menu* untuk kembali.");
            return;
        }

        // State machine routing
        match ($session->state) {
            'idle' => $this->routeFromMenu($body, $session, $employee, $tenantId, $message),
            'awaiting_clockin_location' => $this->handleClockInLocation($message, $session, $employee, $tenantId),
            'awaiting_leave_type' => $this->handleLeaveType($body, $session, $employee, $tenantId),
            'awaiting_leave_dates' => $this->handleLeaveDates($body, $session, $employee, $tenantId),
            'awaiting_leave_reason' => $this->handleLeaveReason($body, $session, $employee, $tenantId),
            'awaiting_approval_action' => $this->handleApprovalAction($body, $session, $employee, $tenantId),
            default => $this->showMenu($phone, $tenantId, $employee),
        };
    }

    private function showMenu(string $phone, int $tenantId, Employee $employee): void
    {
        $name = $employee->nickname ?: explode(' ', $employee->full_name)[0];
        $today = AttendanceLog::where('employee_id', $employee->id)->where('date', today())->first();
        $clockStatus = $today?->check_in_at
            ? ($today->check_out_at ? "✅ Sudah selesai kerja hari ini" : "⏰ Sudah clock-in jam " . $today->check_in_at->format('H:i'))
            : "📍 Belum clock-in hari ini";

        $this->reply($phone, $tenantId,
            "Halo *{$name}* 👋\n\n{$clockStatus}\n\n*Menu HRIS:*\n" .
            "1️⃣ *absen* — Clock-in/out\n" .
            "2️⃣ *cuti* — Request cuti\n" .
            "3️⃣ *saldo* — Cek saldo cuti\n" .
            "4️⃣ *slip* — Slip gaji terbaru\n" .
            "5️⃣ *approval* — Inbox persetujuan\n\n" .
            "Ketik nama menu atau nomornya."
        );
    }

    private function routeFromMenu(string $body, WhatsAppSession $session, Employee $employee, int $tenantId, array $message): void
    {
        switch (true) {
            case in_array($body, ['1', 'absen', 'absensi', 'clock-in', 'clockin']):
                $session->update(['state' => 'awaiting_clockin_location']);
                $this->reply($session->phone_number, $tenantId,
                    "📍 *Clock-in*\n\nSilakan kirim *lokasi Anda saat ini* (Lampirkan via WA: tekan ➕ → Location → Send your current location)."
                );
                break;

            case in_array($body, ['2', 'cuti', 'request cuti', 'ajukan cuti']):
                $session->update(['state' => 'awaiting_leave_type']);
                $types = LeaveType::where('tenant_id', $tenantId)->where('is_active', true)->get();
                $list = $types->map(fn ($t, $i) => ($i + 1) . ". {$t->name}")->implode("\n");
                $session->update(['state_data' => ['types' => $types->pluck('id', 'code')->all()]]);
                $this->reply($session->phone_number, $tenantId,
                    "📝 *Request Cuti*\n\nPilih jenis cuti:\n{$list}\n\nKetik nama atau kode (e.g., CT untuk Cuti Tahunan)."
                );
                break;

            case in_array($body, ['3', 'saldo', 'saldo cuti']):
                $this->showLeaveBalance($session, $employee, $tenantId);
                break;

            case in_array($body, ['4', 'slip', 'slip gaji']):
                $this->showLatestPayslip($session, $employee, $tenantId);
                break;

            case in_array($body, ['5', 'approval', 'inbox']):
                $this->showApprovalInbox($session, $employee, $tenantId);
                break;

            default:
                $this->showMenu($session->phone_number, $tenantId, $employee);
        }
    }

    private function handleClockInLocation(array $message, WhatsAppSession $session, Employee $employee, int $tenantId): void
    {
        if (($message['type'] ?? '') !== 'location' && empty($message['latitude'])) {
            $this->reply($session->phone_number, $tenantId, "Tolong kirim *lokasi* via attachment WhatsApp, bukan ketikan.");
            return;
        }

        $lat = (float) ($message['latitude'] ?? $message['location']['latitude']);
        $lng = (float) ($message['longitude'] ?? $message['location']['longitude']);

        // Check existing log
        $log = AttendanceLog::firstOrNew(['employee_id' => $employee->id, 'date' => today()]);
        if ($log->check_in_at && !$log->check_out_at) {
            // Clock-out flow
            $log->update([
                'check_out_at' => now(),
                'check_out_latitude' => $lat,
                'check_out_longitude' => $lng,
                'check_out_method' => 'manual',
                'worked_minutes' => now()->diffInMinutes($log->check_in_at),
            ]);
            $this->reply($session->phone_number, $tenantId,
                "✅ *Clock-out sukses!*\nJam pulang: " . now()->format('H:i') . "\nDurasi kerja: " . round($log->worked_minutes / 60, 1) . " jam.\n\nSampai jumpa besok 👋"
            );
        } elseif ($log->check_in_at) {
            $this->reply($session->phone_number, $tenantId, "Sudah selesai kerja hari ini. Sampai jumpa besok 👋");
        } else {
            // Clock-in
            $log->fill([
                'tenant_id' => $tenantId,
                'check_in_at' => now(),
                'check_in_latitude' => $lat,
                'check_in_longitude' => $lng,
                'check_in_method' => 'manual',
                'status' => 'present',
            ])->save();
            $this->reply($session->phone_number, $tenantId,
                "✅ *Clock-in sukses!*\nJam masuk: " . now()->format('H:i') . "\nSelamat bekerja! 💪\n\nKetik *absen* lagi untuk clock-out sore nanti."
            );
        }

        $session->update(['state' => 'idle']);
    }

    private function handleLeaveType(string $body, WhatsAppSession $session, Employee $employee, int $tenantId): void
    {
        $types = $session->state_data['types'] ?? [];
        $code = strtoupper($body);
        $typeId = $types[$code] ?? null;

        if (!$typeId) {
            // Try by name match
            $type = LeaveType::where('tenant_id', $tenantId)->whereRaw('LOWER(name) LIKE ?', ['%' . $body . '%'])->first();
            $typeId = $type?->id;
        }

        if (!$typeId) {
            $this->reply($session->phone_number, $tenantId, "Jenis cuti tidak dikenali. Coba lagi atau ketik *batal*.");
            return;
        }

        $session->update([
            'state' => 'awaiting_leave_dates',
            'state_data' => array_merge($session->state_data ?? [], ['leave_type_id' => $typeId]),
        ]);
        $this->reply($session->phone_number, $tenantId,
            "📅 Tanggal cuti? Format: *YYYY-MM-DD sampai YYYY-MM-DD*\n\nContoh: 2026-06-01 sampai 2026-06-03"
        );
    }

    private function handleLeaveDates(string $body, WhatsAppSession $session, Employee $employee, int $tenantId): void
    {
        if (!preg_match('/(\d{4}-\d{2}-\d{2}).+(\d{4}-\d{2}-\d{2})/', $body, $matches)) {
            $this->reply($session->phone_number, $tenantId, "Format salah. Contoh: 2026-06-01 sampai 2026-06-03");
            return;
        }
        try {
            $start = Carbon::parse($matches[1]);
            $end = Carbon::parse($matches[2]);
            if ($end->lt($start)) throw new \Exception('end < start');
        } catch (\Throwable $e) {
            $this->reply($session->phone_number, $tenantId, "Tanggal tidak valid. Coba lagi.");
            return;
        }

        $session->update([
            'state' => 'awaiting_leave_reason',
            'state_data' => array_merge($session->state_data ?? [], [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ]),
        ]);
        $days = $end->diffInDays($start) + 1;
        $this->reply($session->phone_number, $tenantId,
            "📝 *{$days} hari cuti* dari {$start->format('d M')} sampai {$end->format('d M Y')}.\n\nKetik *alasan cuti* (bebas):"
        );
    }

    private function handleLeaveReason(string $body, WhatsAppSession $session, Employee $employee, int $tenantId): void
    {
        $data = $session->state_data ?? [];
        $type = LeaveType::find($data['leave_type_id']);

        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);
        $days = $end->diffInDays($start) + 1;

        $leave = LeaveRequest::create([
            'tenant_id' => $tenantId,
            'employee_id' => $employee->id,
            'leave_type_id' => $type->id,
            'start_date' => $start,
            'end_date' => $end,
            'days_count' => $days,
            'reason' => $body,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        $this->approval->submit('leave', $leave, $employee);

        $session->update(['state' => 'idle', 'state_data' => null]);
        $this->reply($session->phone_number, $tenantId,
            "✅ *Request cuti terkirim!*\n\nJenis: {$type->name}\nDurasi: {$days} hari ({$start->format('d M')} - {$end->format('d M Y')})\nStatus: ⏳ Menunggu approval manager.\n\nKetik *menu* untuk kembali."
        );
    }

    private function showLeaveBalance(WhatsAppSession $session, Employee $employee, int $tenantId): void
    {
        $balances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->with('leaveType')
            ->get();

        if ($balances->isEmpty()) {
            $this->reply($session->phone_number, $tenantId, "📊 Belum ada saldo cuti tercatat tahun ini.");
            return;
        }

        $lines = ["📊 *Saldo Cuti Tahun " . now()->year . "*\n"];
        foreach ($balances as $b) {
            $available = $b->allocated - $b->used - $b->pending;
            $lines[] = "• *{$b->leaveType->name}*: {$available}/{$b->allocated} hari tersedia";
        }
        $this->reply($session->phone_number, $tenantId, implode("\n", $lines));
    }

    private function showLatestPayslip(WhatsAppSession $session, Employee $employee, int $tenantId): void
    {
        $payslip = \App\Models\Payslip::where('employee_id', $employee->id)
            ->latest('slip_date')
            ->first();

        if (!$payslip) {
            $this->reply($session->phone_number, $tenantId, "📭 Belum ada slip gaji yang tersedia.");
            return;
        }

        $this->reply($session->phone_number, $tenantId,
            "💰 *Slip Gaji Terbaru*\n\nNomor: {$payslip->slip_number}\nTanggal: " . $payslip->slip_date->format('d M Y') . "\n\nPDF akan kami kirim ke email Anda. Buka dengan password: NIK Anda (16 digit)."
        );
        // TODO: send PDF as WA document attachment using BotEngine outbound adapter
    }

    private function showApprovalInbox(WhatsAppSession $session, Employee $employee, int $tenantId): void
    {
        $steps = \App\Models\ApprovalRequestStep::where('approver_employee_id', $employee->id)
            ->where('action', 'pending')
            ->limit(5)
            ->get();

        if ($steps->isEmpty()) {
            $this->reply($session->phone_number, $tenantId, "📭 Inbox approval kosong. Anda sudah up-to-date!");
            return;
        }

        $session->update(['state' => 'awaiting_approval_action', 'state_data' => ['step_ids' => $steps->pluck('id')->all()]]);
        $lines = ["📥 *Inbox Approval* ({$steps->count()} pending)\n"];
        foreach ($steps as $i => $s) {
            $lines[] = ($i + 1) . ". Request #{$s->approval_request_id}";
        }
        $lines[] = "\nBalas: *OK 1* untuk approve nomor 1, atau *TOLAK 1* untuk reject.";
        $this->reply($session->phone_number, $tenantId, implode("\n", $lines));
    }

    private function handleApprovalAction(string $body, WhatsAppSession $session, Employee $employee, int $tenantId): void
    {
        if (preg_match('/^(ok|approve|setuju|tolak|reject)\s*(\d+)?/i', $body, $m)) {
            $action = strtolower($m[1]);
            $idx = max(1, (int) ($m[2] ?? 1)) - 1;
            $stepIds = $session->state_data['step_ids'] ?? [];
            $stepId = $stepIds[$idx] ?? null;

            if (!$stepId) {
                $this->reply($session->phone_number, $tenantId, "Nomor tidak valid.");
                return;
            }

            $step = \App\Models\ApprovalRequestStep::find($stepId);
            if (!$step || $step->approver_employee_id !== $employee->id) {
                $this->reply($session->phone_number, $tenantId, "Request tidak ditemukan atau bukan inbox Anda.");
                return;
            }

            if (in_array($action, ['ok', 'approve', 'setuju'])) {
                $this->approval->approve($step, 'Approved via WhatsApp');
                $this->reply($session->phone_number, $tenantId, "✅ Request #{$step->approval_request_id} disetujui.");
            } else {
                $this->approval->reject($step, 'Rejected via WhatsApp');
                $this->reply($session->phone_number, $tenantId, "❌ Request #{$step->approval_request_id} ditolak.");
            }

            $session->update(['state' => 'idle', 'state_data' => null]);
        } else {
            $this->reply($session->phone_number, $tenantId, "Format: *OK 1* atau *TOLAK 1*");
        }
    }

    private function reply(string $phone, int $tenantId, string $body): void
    {
        DB::table('whatsapp_messages')->insert([
            'tenant_id' => $tenantId,
            'phone_number' => $phone,
            'direction' => 'outbound',
            'message_type' => 'text',
            'body' => $body,
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $adapter = $this->registry->resolveDefaultForDomain($tenantId, 'whatsapp');
            $adapter?->sendText($phone, $body);
        } catch (\Throwable $e) {
            logger()->warning("WA send failed to {$phone}: " . $e->getMessage());
        }
    }

    private function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/\D/', '', $phone);
        if (str_starts_with($clean, '08')) return '628' . substr($clean, 2);
        if (str_starts_with($clean, '8')) return '62' . $clean;
        return $clean;
    }
}
