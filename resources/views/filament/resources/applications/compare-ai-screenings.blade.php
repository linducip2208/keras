<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Application header --}}
        <div class="bg-gradient-to-br from-pink-50 via-purple-50 to-orange-50 rounded-2xl p-6 ring-1 ring-pink-200">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <div class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-1">Kandidat</div>
                    <h2 class="text-2xl font-extrabold text-gray-900">{{ $record->candidate->full_name }}</h2>
                    <div class="text-sm text-gray-600 mt-1">
                        📧 {{ $record->candidate->email }} ·
                        📍 {{ $record->candidate->location ?? '-' }} ·
                        💼 {{ $record->candidate->current_position ?? '-' }} ·
                        ⏳ {{ $record->candidate->years_experience ?? 0 }} tahun
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs font-bold text-pink-600 uppercase tracking-wider mb-1">Lowongan</div>
                    <div class="text-lg font-bold text-gray-900">{{ $record->jobPosting->title }}</div>
                    <div class="text-sm text-gray-600">{{ $record->jobPosting->location ?? '-' }}</div>
                </div>
            </div>
        </div>

        @if ($screenings->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 text-center">
                <div class="text-5xl mb-3">🤖</div>
                <h3 class="text-lg font-bold text-yellow-900 mb-1">Belum ada AI screening</h3>
                <p class="text-sm text-yellow-700">Klik tombol "🧠 Screen with AI" di halaman daftar untuk mulai.</p>
            </div>
        @else
            {{-- Best score banner --}}
            @if ($bestScreening)
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 ring-2 ring-emerald-300">
                    <div class="flex items-center gap-4">
                        <div class="text-5xl">🏆</div>
                        <div class="flex-1">
                            <div class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Best Score</div>
                            <div class="text-3xl font-extrabold text-emerald-900">{{ number_format($bestScreening->score, 0) }}/100</div>
                            <div class="text-sm text-emerald-700 mt-1">
                                via <strong>{{ $bestScreening->adapter_name }}</strong>
                                <code class="text-xs bg-white px-1.5 py-0.5 rounded ml-1">{{ $bestScreening->model_used }}</code>
                                → recommendation: <strong>{{ strtoupper(str_replace('_', ' ', $bestScreening->recommendation ?? 'N/A')) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Side-by-side comparison --}}
            <div>
                <h3 class="text-xl font-extrabold mb-4">📊 Perbandingan {{ $screenings->count() }} Screening</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($screenings as $s)
                        @php
                            $scoreColor = match(true) {
                                $s->score >= 80 => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-300', 'text' => 'text-emerald-700', 'badge' => 'bg-emerald-500'],
                                $s->score >= 60 => ['bg' => 'bg-amber-50', 'border' => 'border-amber-300', 'text' => 'text-amber-700', 'badge' => 'bg-amber-500'],
                                $s->score >= 1 => ['bg' => 'bg-red-50', 'border' => 'border-red-300', 'text' => 'text-red-700', 'badge' => 'bg-red-500'],
                                default => ['bg' => 'bg-gray-50', 'border' => 'border-gray-300', 'text' => 'text-gray-700', 'badge' => 'bg-gray-400'],
                            };
                        @endphp
                        <div class="{{ $scoreColor['bg'] }} rounded-2xl p-5 ring-2 {{ $scoreColor['border'] }}">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="flex-1">
                                    <div class="font-bold text-gray-900">{{ $s->adapter_name }}</div>
                                    <code class="text-xs text-gray-500">{{ $s->model_used ?? '(default)' }}</code>
                                </div>
                                @if ($s->status === 'completed')
                                    <div class="{{ $scoreColor['badge'] }} text-white font-extrabold text-lg px-3 py-1 rounded-xl">{{ number_format($s->score ?? 0, 0) }}</div>
                                @elseif ($s->status === 'running')
                                    <div class="bg-blue-500 text-white text-xs px-3 py-1 rounded-xl animate-pulse">RUNNING</div>
                                @else
                                    <div class="bg-red-500 text-white text-xs px-3 py-1 rounded-xl">FAILED</div>
                                @endif
                            </div>

                            @if ($s->status === 'completed')
                                <div class="text-xs uppercase tracking-wider {{ $scoreColor['text'] }} font-bold mb-2">
                                    {{ str_replace('_', ' ', $s->recommendation ?? '') }}
                                </div>

                                @if ($s->summary)
                                    <p class="text-sm text-gray-700 mb-3">{{ $s->summary }}</p>
                                @endif

                                @if (!empty($s->strengths))
                                    <div class="mb-2">
                                        <div class="text-xs font-bold text-emerald-700 mb-1">✓ Strengths</div>
                                        <ul class="text-xs text-gray-700 space-y-0.5">
                                            @foreach ((array)$s->strengths as $str)
                                                <li>• {{ $str }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (!empty($s->weaknesses))
                                    <div class="mb-2">
                                        <div class="text-xs font-bold text-red-700 mb-1">⚠ Weaknesses</div>
                                        <ul class="text-xs text-gray-700 space-y-0.5">
                                            @foreach ((array)$s->weaknesses as $w)
                                                <li>• {{ $w }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if ($s->reasoning)
                                    <details class="mt-3">
                                        <summary class="text-xs font-semibold text-gray-600 cursor-pointer hover:text-gray-900">Lihat reasoning</summary>
                                        <p class="text-xs text-gray-600 mt-2 italic">{{ $s->reasoning }}</p>
                                    </details>
                                @endif

                                @if (!empty($s->usage))
                                    <div class="text-xs text-gray-500 mt-3 pt-3 border-t border-gray-200">
                                        Tokens: {{ $s->usage['total_tokens'] ?? '?' }} ·
                                        {{ $s->created_at->diffForHumans() }}
                                    </div>
                                @endif
                            @elseif ($s->status === 'failed')
                                <p class="text-sm text-red-700">{{ $s->error_message }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
