<div class="space-y-4">
    <div class="flex items-center justify-between">
        <button
            type="button"
            wire:click="previousMonth"
            aria-label="{{ __('app.Previous month') }}"
            class="neu-btn px-3 py-2"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"></path>
            </svg>
        </button>

        <h3 class="font-display text-base font-semibold text-surface-800" aria-live="polite">{{ $this->monthLabel }}</h3>

        <button
            type="button"
            wire:click="nextMonth"
            aria-label="{{ __('app.Next month') }}"
            class="neu-btn px-3 py-2"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </button>
    </div>

    <div class="grid grid-cols-7 gap-2 text-center">
        <span class="text-xs font-medium text-surface-500 py-2">Sen</span>
        <span class="text-xs font-medium text-surface-500 py-2">Sel</span>
        <span class="text-xs font-medium text-surface-500 py-2">Rab</span>
        <span class="text-xs font-medium text-surface-500 py-2">Kam</span>
        <span class="text-xs font-medium text-surface-500 py-2">Jum</span>
        <span class="text-xs font-medium text-surface-500 py-2">Sab</span>
        <span class="text-xs font-medium text-surface-500 py-2">Min</span>
    </div>

    <div class="grid grid-cols-7 gap-2">
        @foreach ($this->calendarRows as $row)
            @foreach ($row as $day)
                @php
                [$year, $month, $date, $inMonth, $isPast, $isUnavailable] = $day;
                $today = now()->startOfDay();
                $isToday = $year == $today->year && $month == $today->month && $date == $today->day;
                @endphp

                <div
                    @class([
                        'neu-inset-sm aspect-square flex flex-col items-center justify-center rounded-neu-sm text-sm',
                        'text-surface-400 bg-surface-200/60' => !$inMonth || $isPast,
                        'text-surface-500' => $inMonth && $isPast,
                        'text-surface-800 font-medium' => $inMonth && !$isPast && !$isUnavailable,
                        'bg-clay-100/40 text-clay-700 font-medium shadow-neu-inset-sm' => $inMonth && !$isPast && $isUnavailable,
                        'ring-2 ring-clay-400/60' => $isToday,
                    ])
                    title="{{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($date, 2, '0', STR_PAD_LEFT) }}"
                >
                    <span>{{ $date }}</span>
                    @if ($inMonth && !$isPast && $isUnavailable)
                        <span class="text-[10px] text-clay-600 mt-0.5">{{ __('app.Unavailable') }}</span>
                    @endif
                </div>
            @endforeach
        @endforeach
    </div>

    <div class="flex items-center gap-4 text-xs text-surface-500 pt-2">
        <div class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-sm bg-surface-200 shadow-neu-inset-sm"></span>
            <span>Tersedia</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-sm bg-clay-100/40 shadow-neu-inset-sm"></span>
            <span>{{ __('app.Unavailable') }}</span>
        </div>
    </div>
</div>
