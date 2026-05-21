<?php

namespace App\Filament\Resources\Employees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&background=f97316&color=fff'),
                TextColumn::make('employee_no')->label('No.')->searchable()->sortable(),
                TextColumn::make('full_name')->label('Nama Lengkap')->searchable()->sortable()
                    ->description(fn ($record) => $record->jobTitle?->name),
                TextColumn::make('company.name')->label('Perusahaan')->sortable(),
                TextColumn::make('department.name')->label('Dept')->badge()->color('info'),
                TextColumn::make('branch.name')->label('Cabang')->badge()->color('gray'),
                TextColumn::make('employment_status')->label('Status')->badge()
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'probation' => 'warning',
                        'on_leave' => 'info',
                        'suspended' => 'danger',
                        'resigned', 'terminated' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('employment_type')->label('Tipe')->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                TextColumn::make('join_date')->label('Bergabung')->date()->sortable(),
                TextColumn::make('manager.full_name')->label('Manager')->toggleable(),
                TextColumn::make('phone')->label('HP')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('employee_email')->label('Email')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('employment_status')
                    ->options([
                        'active'=>'Aktif','probation'=>'Probation','on_leave'=>'Cuti Panjang',
                        'suspended'=>'Suspended','resigned'=>'Resign','terminated'=>'Diberhentikan',
                    ]),
                SelectFilter::make('employment_type')
                    ->options([
                        'permanent'=>'Permanent','contract'=>'Kontrak','probation'=>'Probation',
                        'intern'=>'Magang','outsource'=>'Outsource','freelance'=>'Freelance',
                    ]),
                SelectFilter::make('company_id')->relationship('company', 'name')->label('Perusahaan'),
                SelectFilter::make('department_id')->relationship('department', 'name')->label('Dept'),
                TrashedFilter::make(),
            ])
            ->defaultSort('full_name')
            ->recordActions([
                EditAction::make(),
                \Filament\Actions\Action::make('download_1721_a1')
                    ->label('Download 1721-A1')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function ($record) {
                        $path = app(\App\Services\Tax\TaxForm1721A1Service::class)
                            ->generate($record, now()->year);
                        \Filament\Notifications\Notification::make()
                            ->title('Form 1721-A1 generated')
                            ->body("Saved to: storage/app/private/{$path}")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
