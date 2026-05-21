<x-filament-panels::page>
    <x-filament-panels::form wire:submit="runPayroll">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="[\Filament\Actions\Action::make('runPayroll')->label('Run Payroll Sekarang')->submit('runPayroll')->color('primary')->icon('heroicon-o-rocket-launch')]"
        />
    </x-filament-panels::form>
</x-filament-panels::page>
