<?php

namespace App\Filament\Resources\AttendanceLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('employee.full_name')->label('Karyawan')->searchable()
                    ->description(fn ($record) => $record->employee?->employee_no),
                TextColumn::make('shift.name')->label('Shift')->badge()->toggleable(),
                TextColumn::make('check_in_at')->label('Masuk')
                    ->dateTime('H:i')
                    ->color(fn ($record) => $record->check_in_is_late ? 'danger' : 'success')
                    ->icon(fn ($record) => $record->check_in_is_late ? 'heroicon-o-exclamation-circle' : null),
                TextColumn::make('check_out_at')->label('Pulang')->dateTime('H:i'),
                TextColumn::make('worked_minutes')->label('Kerja')
                    ->formatStateUsing(fn ($state) => $state ? sprintf('%dj %dm', intdiv($state, 60), $state % 60) : '-'),
                TextColumn::make('check_in_method')->label('Metode')->badge()->toggleable()
                    ->color(fn ($state) => match ($state) {
                        'gps' => 'success', 'fingerprint' => 'info', 'qr' => 'primary',
                        'face' => 'warning', 'manual' => 'gray', default => 'gray',
                    }),
                IconColumn::make('check_in_is_mock_gps')->label('🚨 Mock GPS')->boolean()
                    ->trueIcon('heroicon-o-shield-exclamation')->trueColor('danger')
                    ->falseIcon('')->toggleable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn ($state) => match ($state) {
                        'present' => 'success', 'absent' => 'danger', 'leave' => 'info',
                        'half_day' => 'warning', 'holiday' => 'gray', 'weekend' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'present'=>'Hadir','absent'=>'Tidak hadir','leave'=>'Cuti',
                    'half_day'=>'Half day','holiday'=>'Libur','weekend'=>'Weekend',
                ]),
                SelectFilter::make('check_in_method')->label('Metode')->options([
                    'gps'=>'GPS','fingerprint'=>'Fingerprint','qr'=>'QR','face'=>'Face','manual'=>'Manual',
                ]),
                Filter::make('late')->label('Telat saja')
                    ->query(fn (Builder $q) => $q->where('check_in_is_late', true)),
                Filter::make('mock_gps')->label('🚨 Suspect Mock GPS')
                    ->query(fn (Builder $q) => $q->where('check_in_is_mock_gps', true)),
                Filter::make('today')->label('Hari ini')
                    ->query(fn (Builder $q) => $q->whereDate('date', today())),
            ])
            ->defaultSort('date', 'desc')
            ->recordActions([ViewAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
