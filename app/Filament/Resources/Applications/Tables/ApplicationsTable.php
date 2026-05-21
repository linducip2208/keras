<?php

namespace App\Filament\Resources\Applications\Tables;

use App\Models\AdapterConfig;
use App\Services\AI\CVScreeningService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->prefix('#')->sortable(),
                TextColumn::make('candidate.full_name')->label('Kandidat')->searchable()
                    ->description(fn ($record) => $record?->candidate?->email),
                TextColumn::make('jobPosting.title')->label('Lowongan')->searchable()->limit(40),
                TextColumn::make('ai_match_score')->label('AI Score')->sortable()
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0) . '/100' : '—')
                    ->color(fn ($state) => match(true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state >= 1 => 'danger',
                        default => 'gray',
                    })
                    ->badge(),
                TextColumn::make('ai_screenings_count')->label('AI Runs')
                    ->counts('aiScreenings')
                    ->toggleable(),
                TextColumn::make('salary_expectation')->label('Expected')->money('IDR')->toggleable(),
                TextColumn::make('notice_period_days')->label('Notice')->suffix(' hari')->toggleable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn ($state) => match($state) {
                        'active' => 'info', 'hired' => 'success',
                        'rejected', 'withdrawn' => 'danger',
                        'on_hold' => 'warning', default => 'gray',
                    }),
                TextColumn::make('applied_at')->label('Apply')->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'active'=>'Active','hired'=>'Hired','rejected'=>'Rejected',
                    'withdrawn'=>'Withdrawn','on_hold'=>'On Hold',
                ]),
                SelectFilter::make('job_posting_id')->relationship('jobPosting', 'title')->label('Lowongan'),
                TrashedFilter::make(),
            ])
            ->defaultSort('applied_at', 'desc')
            ->recordActions([
                // 🧠 AI Screen — multi-provider picker
                Action::make('ai_screen')
                    ->label('🧠 Screen with AI')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('info')
                    ->schema(function ($record) {
                        $adapters = AdapterConfig::where('tenant_id', $record->tenant_id)
                            ->where('domain', 'llm')
                            ->where('is_active', true)
                            ->get();

                        if ($adapters->isEmpty()) {
                            return [
                                Placeholder::make('no_adapter')
                                    ->label('')
                                    ->content(new HtmlString('
                                        <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-lg">
                                            ⚠️ <strong>Belum ada LLM provider yang dikonfigurasi.</strong><br>
                                            Buka <a href="/admin/adapter-configs/create" class="underline">Settings → Integrasi (BYOK)</a>
                                            dan tambah provider (OpenAI / DeepSeek / Groq / Ollama / dll.) dengan domain "llm".
                                        </div>
                                    ')),
                            ];
                        }

                        return [
                            Select::make('adapter_config_id')
                                ->label('AI Provider')
                                ->options($adapters->pluck('name', 'id'))
                                ->required()
                                ->reactive()
                                ->live()
                                ->helperText('Pilih provider AI. Setiap provider memberi score berbeda — bisa run multiple untuk comparison.'),

                            TextInput::make('model')
                                ->label('Model')
                                ->placeholder('e.g., gpt-4o-mini, deepseek-chat, llama-3.3-70b, qwen-2.5-72b')
                                ->helperText('Kosongkan untuk pakai default model adapter.')
                                ->reactive()
                                ->live(debounce: 400),

                            Placeholder::make('preview')
                                ->label('Preview')
                                ->content(function ($get) use ($record, $adapters) {
                                    $adapterId = $get('adapter_config_id');
                                    $model = $get('model') ?: '(default)';
                                    if (!$adapterId) return new HtmlString('<em class="text-gray-400">Pilih provider untuk lihat preview</em>');
                                    $adapter = $adapters->firstWhere('id', $adapterId);
                                    $previousCount = \App\Models\AIScreening::where('application_id', $record->id)
                                        ->where('adapter_config_id', $adapterId)->count();
                                    return new HtmlString("
                                        <div class='space-y-1 text-sm'>
                                            <div>🔌 Provider: <strong>{$adapter->name}</strong></div>
                                            <div>⚙️ Format: <code class='bg-gray-100 px-1 rounded'>{$adapter->format}</code></div>
                                            <div>🤖 Model: <code class='bg-gray-100 px-1 rounded'>{$model}</code></div>
                                            <div>📊 Previous runs dgn provider ini: <strong>{$previousCount}</strong></div>
                                        </div>
                                    ");
                                }),

                            Placeholder::make('history')
                                ->label('🕒 Riwayat Screening Application Ini')
                                ->content(function () use ($record) {
                                    $history = \App\Models\AIScreening::where('application_id', $record->id)
                                        ->orderByDesc('created_at')->limit(5)->get();
                                    if ($history->isEmpty()) return new HtmlString('<em class="text-gray-400">Belum ada screening</em>');
                                    $rows = $history->map(function ($s) {
                                        $score = $s->score ? number_format($s->score, 0) . '/100' : '—';
                                        $color = $s->score >= 80 ? 'green' : ($s->score >= 60 ? 'yellow' : 'red');
                                        return "<tr>
                                            <td class='py-1'><strong>{$s->adapter_name}</strong> <code class='text-xs text-gray-500'>{$s->model_used}</code></td>
                                            <td class='py-1 text-{$color}-600 font-bold text-right'>{$score}</td>
                                            <td class='py-1 text-right text-xs text-gray-500'>{$s->recommendation}</td>
                                            <td class='py-1 text-right text-xs text-gray-400'>{$s->created_at->diffForHumans()}</td>
                                        </tr>";
                                    })->implode('');
                                    return new HtmlString("<table class='w-full text-sm'>{$rows}</table>");
                                }),
                        ];
                    })
                    ->action(function (array $data, $record) {
                        $adapter = AdapterConfig::find($data['adapter_config_id']);
                        if (!$adapter) {
                            Notification::make()->title('Adapter not found')->danger()->send();
                            return;
                        }

                        $screening = app(CVScreeningService::class)->screen(
                            $record,
                            $adapter,
                            $data['model'] ?? null,
                            auth()->id(),
                        );

                        if ($screening->status === 'completed') {
                            Notification::make()
                                ->title("AI Score: " . number_format($screening->score ?? 0, 0) . "/100")
                                ->body("{$screening->adapter_name} → {$screening->recommendation}")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Screening failed')
                                ->body($screening->error_message)
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalSubmitActionLabel('Jalankan AI Screening')
                    ->modalWidth('xl'),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
