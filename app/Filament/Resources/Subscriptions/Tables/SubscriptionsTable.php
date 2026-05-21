<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Models\Plan;
use App\Services\Billing\BillingService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('Tenant')->searchable(),
                TextColumn::make('plan.name')->label('Plan')->badge()->color('info'),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn ($state) => match($state) {
                        'active' => 'success', 'trial' => 'info',
                        'past_due' => 'warning', 'canceled', 'expired', 'suspended' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('billing_cycle')->label('Siklus')->badge(),
                TextColumn::make('seats')->label('Seats')->numeric()->sortable(),
                TextColumn::make('current_period_start')->label('Mulai')->date('d M Y'),
                TextColumn::make('current_period_end')->label('Berakhir')->date('d M Y')
                    ->color(fn ($state) => $state && now()->gt($state) ? 'danger' : null),
                IconColumn::make('auto_renew')->label('Auto-renew')->boolean(),
                TextColumn::make('trial_ends_at')->label('Trial End')->date('d M Y')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'trial'=>'Trial','active'=>'Active','past_due'=>'Past Due',
                    'canceled'=>'Canceled','expired'=>'Expired','suspended'=>'Suspended',
                ]),
                SelectFilter::make('plan_id')->relationship('plan', 'name'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('upgrade')
                    ->label('Upgrade Plan')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['active', 'trial']))
                    ->schema(fn ($record) => [
                        Select::make('plan_id')
                            ->label('Plan Baru')
                            ->options(Plan::where('id', '!=', $record->plan_id)->where('is_active', true)
                                ->get()->mapWithKeys(fn ($p) => [
                                    $p->id => "{$p->name} — Rp " . number_format($p->price_monthly, 0, ',', '.') . "/user/bln",
                                ]))
                            ->required(),
                        Select::make('billing_cycle')
                            ->label('Siklus Billing')
                            ->options(['monthly' => 'Bulanan', 'annual' => 'Tahunan (save 15%)'])
                            ->default($record->billing_cycle)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'plan_id' => $data['plan_id'],
                            'billing_cycle' => $data['billing_cycle'],
                        ]);
                        // Generate proration invoice — disederhanakan: new invoice next cycle
                        $invoice = app(BillingService::class)->generateNextInvoice($record->fresh());
                        Notification::make()
                            ->title('Plan diupgrade')
                            ->body("Invoice baru: {$invoice->number} — Rp " . number_format($invoice->total_amount, 0, ',', '.'))
                            ->success()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Cancel Subscription')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => in_array($record->status, ['active', 'trial', 'past_due']))
                    ->schema([
                        Textarea::make('reason')
                            ->label('Alasan Cancel (untuk feedback)')
                            ->rows(3)
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->modalDescription('Subscription akan aktif sampai akhir periode billing saat ini, lalu otomatis expired.')
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'canceled',
                            'canceled_at' => now(),
                            'auto_renew' => false,
                        ]);
                        Notification::make()
                            ->title('Subscription canceled')
                            ->body('Aktif sampai ' . $record->current_period_end->format('d M Y'))
                            ->warning()
                            ->send();
                    }),
                Action::make('reactivate')
                    ->label('Reactivate')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['canceled', 'expired']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'active',
                            'canceled_at' => null,
                            'auto_renew' => true,
                        ]);
                        Notification::make()->title('Subscription reactivated')->success()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
