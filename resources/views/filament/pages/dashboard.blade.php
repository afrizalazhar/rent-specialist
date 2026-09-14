<x-filament-panels::page class="fi-dashboard-page">
    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    {{-- ---------------------------------------------------------------
         Hero welcome strip — makes the custom look obvious immediately.
         Pure Blade + Tailwind utilities, no JS, no Filament components
         that could break. Inline styles for the gradient so it works
         even if Tailwind purges arbitrary values.
         --------------------------------------------------------------- --}}
    <section
        class="fi-dashboard-hero relative overflow-hidden rounded-2xl px-6 py-7 sm:px-10 sm:py-10"
        style="background: linear-gradient(135deg, #0a0a0a 0%, #1f2937 100%); box-shadow: 8px 8px 20px rgba(0,0,0,0.18), -6px -6px 16px rgba(255,255,255,0.85);"
    >
        <div class="relative z-10 flex flex-col gap-3 sm:flex-row items-start sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">
                    {{ __('filament.Dashboard Greeting') }}
                </p>
                <h1 class="mt-2 font-display text-2xl font-bold text-white sm:text-3xl">
                    {{ __('filament.Welcome Back') }}, {{ auth()->user()?->name ?? config('app.name') }}
                </h1>
                <p class="mt-2 max-w-xl text-sm text-white/70">
                    {{ __('filament.Dashboard Tagline') }}
                </p>
            </div>

            <div class="hidden text-right text-white/70 sm:block">
                <p class="font-display text-lg font-semibold text-white">
                    {{ now()->format('l') }}
                </p>
                <p class="text-sm">{{ now()->format('d F Y') }}</p>
            </div>
        </div>

        {{-- Decorative blob so the hero doesn't feel flat. --}}
        <div
            aria-hidden="true"
            class="absolute -right-16 -top-16 h-56 w-56 rounded-full"
            style="background: radial-gradient(circle, rgba(255,255,255,0.10) 0%, rgba(255,255,255,0) 70%);"
        ></div>
    </section>

    {{-- ---------------------------------------------------------------
         The registered widgets (Stats, Revenue, Recent Bookings, Fleet).
         --------------------------------------------------------------- --}}
    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                ...$this->getWidgetData(),
            ]
        "
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>