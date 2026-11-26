<?php

namespace App\Filament\Resources\ApprovalRequests\Tables;

use App\Models\Employee;
use App\Services\Approval\ApprovalEngine;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ApprovalRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workflow.name')
                    ->label('Workflow')
                    ->searchable(),
                TextColumn::make('requestable_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->searchable(),
                TextColumn::make('requester_employee_id')
                    ->label('Pemohon')
                    ->formatStateUsing(fn ($state): string => Employee::find($state)?->full_name ?? ('#' . $state))
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('requester', fn ($q) => $q->where('full_name', 'like', "%{$search}%"));
                    }),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('current_step_order')
                    ->label('Step')
                    ->formatStateUsing(fn ($record): string => $record->current_step_order . '/' . $record->total_steps)
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn ($record): bool => static::canAct($record))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $step = static::pendingStep($record);
                        abort_if(!$step, 403, 'Tidak ada step pending untuk Anda.');
                        app(ApprovalEngine::class)->approve($step, 'Approved via admin panel');
                        Notification::make()->title('Approved')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn ($record): bool => static::canAct($record))
                    ->schema([Textarea::make('comment')->label('Alasan')->required()])
                    ->action(function ($record, array $data) {
                        $step = static::pendingStep($record);
                        abort_if(!$step, 403, 'Tidak ada step pending untuk Anda.');
                        app(ApprovalEngine::class)->reject($step, $data['comment']);
                        Notification::make()->title('Rejected')->success()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Hanya approver yang ditunjuk pada step pending (atau super_admin)
     * yang boleh approve/reject dari panel.
     */
    protected static function canAct($record): bool
    {
        if ($record->status !== 'pending') {
            return false;
        }
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        if ($user->is_super_admin) {
            return true;
        }
        $employeeId = Employee::where('user_id', $user->id)->value('id');
        if (!$employeeId) {
            return false;
        }
        return $record->steps()->where('action', 'pending')
            ->where('approver_employee_id', $employeeId)->exists();
    }

    protected static function pendingStep($record)
    {
        $user = auth()->user();
        $query = $record->steps()->where('action', 'pending')->orderBy('step_order');
        if (!$user->is_super_admin) {
            $employeeId = Employee::where('user_id', $user->id)->value('id');
            $query->where('approver_employee_id', $employeeId);
        }
        return $query->first();
    }
}

