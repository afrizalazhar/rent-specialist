<x-filament-widgets::widget class="fi-wi-fleet-status">
    <x-filament::section :heading="$this->getHeading()">
        <div class="space-y-4">
            @forelse ($this->getStatuses() as $row)
                <div class="group">
                    <div class="mb-1.5 flex items-center justify-between text-sm">
                        <span class="font-medium text-surface-700 dark:text-surface-300">
                            {{ $row['label'] }}
                        </span>
                        <span class="text-surface-500 dark:text-surface-400">
                            {{ $row['count'] }}
                            <span class="text-xs">({{ $row['percentage'] }}%)</span>
                        </span>
                    </div>

                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-surface-200 dark:bg-surface-800 shadow-neu-inset-sm">
                        <div
                            class="h-full rounded-full {{ $row['color'] }} transition-all duration-500"
                            style="width: {{ max(0, $row['percentage']) }}%"
                        ></div>
                    </div>
                </div>
            @empty
                <div class="py-4 text-center text-sm text-surface-500">
                    {{ __('filament.No vehicles yet') }}
                </div>
            @endforelse

            <div class="mt-4 flex items-center justify-between border-t border-surface-200 pt-3 text-sm dark:border-surface-700/50">
                <span class="font-medium text-surface-700 dark:text-surface-300">{{ __('filament.Total Fleet') }}</span>
                <span class="font-semibold text-surface-900 dark:text-white">{{ $this->getTotal() }}</span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
