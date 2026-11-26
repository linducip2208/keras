<?php

namespace App\Filament\Pages;

use App\Models\Company;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Services\Payroll\PayrollCalculationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PayrollWizard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static ?string $navigationLabel = 'Run Payroll';
    protected static ?string $title = 'Payroll Wizard';
    protected static \UnitEnum|string|null $navigationGroup = '💰  Payroll';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.payroll-wizard';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'period_name' => now()->format('Y-m'),
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
            'cutoff_attendance_date' => now()->endOfMonth()->subDays(5)->toDateString(),
            'payroll_date' => now()->endOfMonth()->toDateString(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Wizard::make([
                    Step::make('Periode')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Section::make('Setup Periode Payroll')
                                ->description('Tentukan periode gaji bulan ini')
                                ->columns(2)
                                ->schema([
                                    Select::make('company_id')->label('Perusahaan')
                                        ->options(fn () => Company::where('tenant_id', auth()->user()->tenant_id)->pluck('name', 'id'))->required()->columnSpanFull(),
                                    TextInput::make('period_name')->label('Nama Periode')->placeholder('2026-05')->required(),
                                    Select::make('period_type')->label('Tipe')
                                        ->options(['monthly'=>'Bulanan','weekly'=>'Mingguan','biweekly'=>'Dua-mingguan','daily'=>'Harian'])
                                        ->default('monthly')->required(),
                                    DatePicker::make('start_date')->label('Mulai')->required()->native(false),
                                    DatePicker::make('end_date')->label('Sampai')->required()->native(false),
                                    DatePicker::make('cutoff_attendance_date')->label('Cut-off Attendance')->required()->native(false)
                                        ->helperText('Tanggal terakhir attendance & lembur di-include'),
                                    DatePicker::make('payroll_date')->label('Tanggal Pembayaran')->required()->native(false),
                                ]),
                        ]),

                    Step::make('Hitung')
                        ->icon('heroicon-o-calculator')
                        ->schema([
                            Section::make('Konfirmasi Sebelum Calculate')
                                ->description('Sistem akan menjalankan kalkulasi payroll background untuk semua karyawan aktif. PPh 21 TER 2024 + BPJS 5 program akan dihitung otomatis.')
                                ->schema([
                                    Select::make('run_type')->label('Jenis Run')
                                        ->options([
                                            'regular' => 'Regular (Bulanan)',
                                            'off_cycle' => 'Off-Cycle (Koreksi)',
                                            'bonus' => 'Bonus',
                                            'thr' => 'THR',
                                        ])->default('regular')->required(),
                                ]),
                        ]),

                    Step::make('Review')
                        ->icon('heroicon-o-magnifying-glass')
                        ->schema([
                            Section::make('Review Output')
                                ->description('Setelah klik Run Payroll, verifikasi angka di menu Payroll Runs → buka run → cek Item Gaji per karyawan. Re-run aman (idempotent per run).')
                                ->schema([]),
                        ]),

                    Step::make('Approve')
                        ->icon('heroicon-o-check-badge')
                        ->schema([
                            Section::make('Approve & Lock')
                                ->description('Approve & lock dilakukan di halaman Edit Payroll Run (ubah status draft → approved → locked), lalu generate slip dan disbursement. Lihat menu Payroll Runs.')
                                ->schema([]),
                        ]),
                ])
                ->submitAction(new Action('runPayroll', Action::make('runPayroll')->label('Run Payroll')->color('primary'))),
            ]);
    }

    public function runPayroll(): void
    {
        $data = $this->data;
        $tenantId = auth()->user()->tenant_id;
        abort_if(!$tenantId, 403, 'Akun tidak terhubung ke tenant.');

        // Cegah periode ganda untuk perusahaan yang sama di rentang yang sama.
        $exists = PayrollPeriod::where('tenant_id', $tenantId)
            ->where('company_id', $data['company_id'])
            ->where('name', $data['period_name'])
            ->exists();
        if ($exists) {
            Notification::make()
                ->title('Periode sudah ada')
                ->body("Periode {$data['period_name']} untuk perusahaan ini sudah dibuat. Buka Payroll Runs untuk re-run.")
                ->warning()
                ->send();
            return;
        }

        try {
            $period = PayrollPeriod::create([
                'tenant_id' => $tenantId,
                'company_id' => $data['company_id'],
                'name' => $data['period_name'],
                'period_type' => $data['period_type'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'cutoff_attendance_date' => $data['cutoff_attendance_date'],
                'payroll_date' => $data['payroll_date'],
                'status' => 'cutoff',
            ]);

            $run = PayrollRun::create([
                'tenant_id' => $tenantId,
                'payroll_period_id' => $period->id,
                'run_type' => $data['run_type'] ?? 'regular',
                'status' => 'draft',
                'created_by_user_id' => auth()->id(),
            ]);

            app(PayrollCalculationService::class)->runForRun($run, $tenantId);
        } catch (\Throwable $e) {
            report($e);
            Notification::make()
                ->title('Payroll gagal dihitung')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return;
        }

        Notification::make()
            ->title('Payroll dihitung')
            ->body("Run #{$run->id} status: {$run->fresh()->status}. Total karyawan: {$run->fresh()->total_employees}.")
            ->success()
            ->send();

        $this->redirect("/admin/payroll-runs/{$run->id}/edit");
    }
}
