<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\AIScreening;
use App\Models\Application;
use Filament\Resources\Pages\Page;

class CompareAiScreenings extends Page
{
    protected static string $resource = ApplicationResource::class;

    protected string $view = 'filament.resources.applications.compare-ai-screenings';

    public Application $record;
    public $screenings;
    public $bestScreening;

    public function mount(int|string $record): void
    {
        $this->record = Application::with(['candidate', 'jobPosting'])->findOrFail($record);

        $this->screenings = AIScreening::where('application_id', $this->record->id)
            ->orderByDesc('score')
            ->get();

        $this->bestScreening = $this->screenings->where('status', 'completed')->first();
    }

    public function getTitle(): string
    {
        return "AI Screening Comparison — #{$this->record->id}";
    }
}
