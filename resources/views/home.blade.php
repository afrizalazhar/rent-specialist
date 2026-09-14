@extends('layouts.app', ['navTheme' => 'dark'])

@section('title', config('business.business.name', 'Rent Specialist') . ' — ' . __('app.Hero title'))
@section('description', __('app.Hero subtitle'))

@section('content')
    {{-- ============================================================
         HERO
         Dark foundation, large headline, two CTAs, vehicle silhouette.
         ============================================================ --}}
    <section class="relative section-ink overflow-hidden">
        {{-- subtle backdrop ornaments --}}
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-32 -right-24 h-[480px] w-[480px] rounded-full bg-accent-500/[0.05] blur-3xl"></div>
            <div class="absolute -bottom-40 -left-32 h-[480px] w-[480px] rounded-full bg-bone-50/[0.04] blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 md:pt-24 pb-20 md:pb-28">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 reveal">
                    <span class="eyebrow-on-dark">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-accent-500"></span>
                        {{ __('app.Hero eyebrow') }}
                    </span>

                    <h1 class="display-xl text-bone-50 mt-5">
                        {{ __('app.Hero title') }}
                    </h1>

                    <p class="mt-6 text-lg md:text-xl text-bone-50/70 leading-relaxed max-w-2xl">
                        {{ __('app.Hero subtitle') }}
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <x-whatsapp-cta :label="__('app.Chat on WhatsApp')" />
                        <a href="#armada" class="btn-ghost-light">
                            {{ __('app.Explore fleet') }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>

                    <ul class="mt-10 flex flex-wrap items-center gap-x-7 gap-y-3 text-sm text-bone-50/70">
                        <li class="inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-accent-400"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            {{ __('app.Trust well maintained') }}
                        </li>
                        <li class="inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-accent-400"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            {{ __('app.Trust transparent') }}
                        </li>
                        <li class="inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-accent-400"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            {{ __('app.Trust fast response') }}
                        </li>
                    </ul>
                </div>

                <div class="lg:col-span-5 reveal">
                    @php
                        // Pre-compute the WhatsApp number once so Alpine can read it
                        // without re-running the regex on every keystroke.
                        $waRaw = config('business.whatsapp_number') ?? config('business.business.phone', '+62 812-3456-7890');
                        $waDigits = preg_replace('/\D+/', '', $waRaw);
                        if (str_starts_with($waDigits, '0')) {
                            $waDigits = '62' . substr($waDigits, 1);
                        }
                        // Unit catalogue serialised for Alpine (id, name, daily_rate).
                        $unitsJson = $units->map(fn ($u) => [
                            'id'    => $u->id,
                            'name'  => $u->displayName(),
                            'rate'  => (int) $u->daily_rate,
                        ])->values();
                        $defaultRate = $units->min('daily_rate') ?: 0;
                    @endphp

                    <form
                        x-data="availabilityForm(@js($unitsJson), {{ $defaultRate }})"
                        @submit.prevent="submit"
                        data-phone="{{ $waDigits }}"
                        data-tpl-unit="{{ __('app.Form vehicle message') }}"
                        data-tpl-any="{{ __('app.Form no unit message') }}"
                        data-from-rate="{{ __('app.Form from rate') }}"
                        data-per-day="{{ __('app.Form per day') }}"
                        data-half-day="{{ __('app.Form half day') }}"
                        data-days-suffix="{{ __('app.Form days suffix') }}"
                        data-past="{{ __('app.Form past date') }}"
                        data-invalid="{{ __('app.Form invalid dates') }}"
                        data-label-from="{{ __('app.Form from rate') }}"
                        class="rounded-premium-lg bg-bone-50 text-ink-900 shadow-premium-lg border border-ink-900/[0.06] overflow-hidden"
                    >
                        <div class="px-5 sm:px-6 pt-5 sm:pt-6 pb-4 border-b border-ink-900/[0.06]">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="font-display text-lg sm:text-xl font-semibold text-ink-900">{{ __('app.Form title') }}</h2>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-accent-500/10 text-accent-700 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.15em]">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-accent-500"></span>
                                    {{ __('app.WhatsApp CTA') }}
                                </span>
                            </div>
                            <p class="text-sm text-ink-500 mt-1.5">{{ __('app.Form subtitle') }}</p>
                        </div>

                        <div class="px-5 sm:px-6 py-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.15em] text-ink-500 mb-1.5">{{ __('app.Form location label') }}</label>
                                <select x-model="location" class="w-full rounded-premium border border-ink-900/[0.08] bg-white px-3.5 py-2.5 text-sm text-ink-900 focus:outline-none focus:ring-2 focus:ring-accent-500/40 focus:border-accent-500 transition-colors duration-150">
                                    <option value="">{{ __('app.Form location placeholder') }}</option>
                                    @foreach ($locations as $loc)
                                        <option value="{{ $loc }}">{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.15em] text-ink-500 mb-1.5">{{ __('app.Form pickup label') }}</label>
                                    <input type="date" x-model="pickup" :min="today" class="w-full rounded-premium border border-ink-900/[0.08] bg-white px-3.5 py-2.5 text-sm text-ink-900 focus:outline-none focus:ring-2 focus:ring-accent-500/40 focus:border-accent-500 transition-colors duration-150">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.15em] text-ink-500 mb-1.5">{{ __('app.Form return label') }}</label>
                                    <input type="date" x-model="ret" :min="pickup || today" class="w-full rounded-premium border border-ink-900/[0.08] bg-white px-3.5 py-2.5 text-sm text-ink-900 focus:outline-none focus:ring-2 focus:ring-accent-500/40 focus:border-accent-500 transition-colors duration-150">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.15em] text-ink-500 mb-1.5">{{ __('app.Form unit label') }}</label>
                                <select x-model="unitId" class="w-full rounded-premium border border-ink-900/[0.08] bg-white px-3.5 py-2.5 text-sm text-ink-900 focus:outline-none focus:ring-2 focus:ring-accent-500/40 focus:border-accent-500 transition-colors duration-150">
                                    <option value="">{{ __('app.Form unit any') }}</option>
                                    @foreach ($units as $u)
                                        <option value="{{ $u->id }}">{{ $u->displayName() }} — @rupiah($u->daily_rate) {{ __('app.Form per day') }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="rounded-premium border border-ink-900/[0.06] bg-bone-100/60 p-4">
                                <div class="flex items-baseline justify-between gap-3 mb-1">
                                    <span class="text-xs font-semibold uppercase tracking-[0.15em] text-ink-500">{{ __('app.Form estimate') }}</span>
                                    <span class="text-[11px] text-ink-400" x-show="rateLabel" x-text="rateLabel"></span>
                                </div>
                                <div class="flex items-baseline gap-2">
                                    <span class="font-display text-2xl sm:text-3xl font-bold text-ink-900 tabular-nums" x-text="formattedCost">Rp 0</span>
                                    <span class="text-sm text-ink-500" x-show="daysLabel" x-text="daysLabel"></span>
                                </div>
                                <p class="text-[11px] text-ink-400 mt-1.5">{{ __('app.Form estimate hint') }}</p>
                            </div>

                            <p class="text-xs text-rust-500 font-medium" x-show="error" x-text="error"></p>

                            <button
                                type="submit"
                                :disabled="!canSubmit"
                                class="btn-accent w-full justify-center !py-3 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:shadow-none"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 0 1 8.413 3.488 11.824 11.824 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24z"/>
                                </svg>
                                {{ __('app.Form cta show cars') }}
                            </button>

                            <p class="text-[11px] text-ink-400 text-center">{{ __('app.Form cta hint') }}</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         PROMO SLIDER — pure image carousel managed from the admin.
         Slides come from the `promo_slides` table; if the collection
         is empty the entire section is hidden so the home page
         doesn't render a broken empty carousel.
         ============================================================ --}}
    @php
        $promoSlidesData = $promoSlides->map(fn ($slide) => [
            'id'        => $slide->id,
            'url'       => $slide->imageUrl(),
            'alt'       => $slide->alt_text ?: $slide->title,
            'cta_label' => $slide->cta_label,
            'cta_url'   => $slide->cta_url,
        ])->values();
    @endphp
    @if ($promoSlidesData->isNotEmpty())
    <section
        class="bg-white"
        x-data="promoSlider(@js($promoSlidesData))"
        @mouseenter="paused = true"
        @mouseleave="paused = false"
        @keydown.window.left="prev()"
        @keydown.window.right="next()"
        aria-roledescription="carousel"
        aria-label="{{ __('app.Services') }}"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">
            <div
                class="relative overflow-hidden rounded-premium-lg shadow-premium-lg bg-ink-900"
                @touchstart.passive="touchStartX = $event.touches[0].clientX"
                @touchend.passive="touchEndX = $event.changedTouches[0].clientX; handleSwipe()"
            >
                <template x-for="(slide, i) in slides" :key="slide.id">
                    <div
                        x-show="active === i"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-x-8"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-8"
                        class="aspect-[16/9] md:aspect-[21/9] relative bg-ink-900"
                        role="group"
                        :aria-roledescription="'slide'"
                        :aria-label="(i + 1) + ' / ' + slides.length"
                    >
                        <img
                            :src="slide.url"
                            :alt="slide.alt"
                            class="absolute inset-0 w-full h-full object-cover"
                            loading="lazy"
                            decoding="async"
                        >
                        {{-- Optional CTA baked into the slide --}}
                        <template x-if="slide.cta_url && slide.cta_label">
                            <a
                                :href="slide.cta_url"
                                target="_blank"
                                rel="noopener"
                                class="absolute bottom-5 right-5 sm:bottom-7 sm:right-7 inline-flex items-center gap-2 rounded-premium bg-accent-500 text-ink-950 px-5 py-2.5 text-sm font-semibold shadow-accent-glow hover:bg-accent-600 transition-colors duration-150"
                                x-text="slide.cta_label"
                            ></a>
                        </template>
                    </div>
                </template>

                {{-- Prev / Next arrows --}}
                <button
                    type="button"
                    @click="prev()"
                    class="absolute left-3 sm:left-5 top-1/2 -translate-y-1/2 z-10 inline-flex items-center justify-center w-11 h-11 rounded-full bg-white/85 hover:bg-white shadow-premium text-ink-900 transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-500"
                    aria-label="{{ __('app.Promo prev') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button
                    type="button"
                    @click="next()"
                    class="absolute right-3 sm:right-5 top-1/2 -translate-y-1/2 z-10 inline-flex items-center justify-center w-11 h-11 rounded-full bg-white/85 hover:bg-white shadow-premium text-ink-900 transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-500"
                    aria-label="{{ __('app.Promo next') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>

            {{-- Controls under the image (on the white section) --}}
            <div class="mt-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <template x-for="i in slides.length" :key="i">
                        <button
                            type="button"
                            @click="goTo(i - 1)"
                            :class="active === (i - 1)
                                ? 'w-8 h-2 rounded-full bg-ink-900'
                                : 'w-2 h-2 rounded-full bg-ink-900/20 hover:bg-ink-900/40'"
                            class="transition-all duration-300"
                            :aria-label="'{{ __('app.Promo slide') }} ' + i"
                            :aria-current="active === (i - 1) ? 'true' : 'false'"
                        ></button>
                    </template>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-[11px] uppercase tracking-[0.18em] text-ink-500 font-semibold tabular-nums">
                        <span x-text="String(active + 1).padStart(2, '0')"></span>
                        <span class="opacity-50">/ <span x-text="String(slides.length).padStart(2, '0')"></span></span>
                    </span>
                    <button
                        type="button"
                        @click="paused = !paused"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-ink-900/15 text-ink-700 hover:text-ink-900 hover:border-ink-900/30 transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-500"
                        :aria-label="paused ? '{{ __('app.Promo resume') }}' : '{{ __('app.Promo pause') }}'"
                        :aria-pressed="paused ? 'true' : 'false'"
                    >
                        <svg x-show="!paused" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
                        <svg x-show="paused" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 4 20 12 6 20 6 4"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         FEATURED FLEET
         ============================================================ --}}
    <section id="armada" class="section-bone">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="max-w-2xl mb-12 md:mb-16 reveal">
                <span class="eyebrow">{{ __('app.Fleet') }}</span>
                <h2 class="display-lg text-ink-900 mt-4">{{ __('app.Featured fleet title') }}</h2>
                <p class="mt-4 text-base md:text-lg text-ink-500 leading-relaxed">{{ __('app.Featured fleet subtitle') }}</p>
            </div>

            @if ($featured->isEmpty())
                <div class="card-light p-10 text-center reveal">
                    <p class="text-ink-500 mb-6">{{ __('app.No vehicles found') }}.</p>
                    <x-whatsapp-cta :label="__('app.Chat on WhatsApp')" />
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach ($featured as $vehicle)
                        @php
                            $photo = $vehicle->photos[0] ?? null;
                            $displayName = $vehicle->displayName();
                            $categoryLabel = $vehicle->type->label();
                            $seats = $vehicle->spec('seats') ?? $vehicle->spec('engine_cc');
                            $transmission = $vehicle->spec('transmission');
                            $transmissionLabel = match (true) {
                                str_contains((string) $transmission, 'auto') || $transmission === 'automatic' => 'Automatic',
                                str_contains((string) $transmission, 'scooter') => 'Automatic',
                                default => 'Manual',
                            };
                            $waMessage = "Halo, saya tertarik dengan {$vehicle->shortName()}. Apakah tersedia untuk tanggal yang saya inginkan?";
                        @endphp

                        <article class="vehicle-card reveal">
                            <a href="{{ route('vehicles.show', $vehicle) }}" class="block vehicle-card-photo">
                                @if ($photo)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($photo) }}" alt="{{ $displayName }}" loading="lazy" style="object-fit: contain!important;">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center px-6 pb-4 bg-gradient-to-br from-bone-100 to-bone-200">
                                        <x-vehicle-silhouette :type="$vehicle->type->value" variant="dark" />
                                    </div>
                                @endif

                                <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 rounded-full bg-ink-950/85 backdrop-blur-sm px-2.5 py-1 text-[11px] font-medium text-bone-50">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-accent-500"></span>
                                    {{ __('app.Available') }}
                                </span>
                            </a>

                            <div class="p-5 md:p-6 flex-1 flex flex-col">
                                <div class="mb-4">
                                    <p class="text-xs uppercase tracking-[0.15em] text-ink-400 mb-1.5">{{ $categoryLabel }}</p>
                                    <h3 class="font-display text-lg md:text-xl font-semibold text-ink-900 leading-snug">
                                        <a href="{{ route('vehicles.show', $vehicle) }}" class="hover:text-accent-600 transition-colors duration-150">
                                            {{ $displayName }}
                                        </a>
                                    </h3>
                                </div>

                                <ul class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-ink-500 mb-5">
                                    @if ($seats)
                                        <li>{{ $seats }} {{ $vehicle->type === \App\Enums\VehicleType::Motorcycle ? 'cc' : 'seats' }}</li>
                                    @endif
                                    @if ($transmission)
                                        <li class="before:content-['·'] before:mr-3 before:text-ink-300">{{ $transmissionLabel }}</li>
                                    @endif
                                </ul>

                                <div class="mt-auto flex items-center justify-between gap-3 pt-4 border-t border-ink-900/[0.06]">
                                    <div>
                                        <p class="text-[11px] uppercase tracking-[0.15em] text-ink-400">{{ __('app.From') }}</p>
                                        <p class="font-display text-base md:text-lg font-semibold text-ink-900">@rupiah($vehicle->daily_rate) <span class="text-xs font-normal text-ink-500">/ {{ __('app.Daily rate') }}</span></p>
                                    </div>
                                    <x-whatsapp-cta
                                        class="!px-4 !py-2 !text-xs"
                                        :message="$waMessage"
                                        :label="__('app.Ask on whatsapp')"
                                    />
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-12 text-center reveal">
                    <a href="{{ route('vehicles.index') }}" class="btn-ghost-dark">
                        {{ __('app.View all vehicles') }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- ============================================================
         WHY CHOOSE US
         ============================================================ --}}
    <section id="mengapa-kami" class="section-bone-soft">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="max-w-2xl mb-12 md:mb-16 reveal">
                <span class="eyebrow">{{ __('app.Footer why us') }}</span>
                <h2 class="display-lg text-ink-900 mt-4">{{ __('app.Why title') }}</h2>
                <p class="mt-4 text-base md:text-lg text-ink-500 leading-relaxed">{{ __('app.Why subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-6">
                <article class="card-light p-6 md:p-7 reveal">
                    <div class="inline-flex items-center justify-center w-11 h-11 rounded-premium bg-accent-500/10 text-accent-600 mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3 4-3 9-3 9 1.34 9 3z"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-ink-900 mb-2">{{ __('app.Benefit well maintained title') }}</h3>
                    <p class="text-sm text-ink-500 leading-relaxed">{{ __('app.Benefit well maintained body') }}</p>
                </article>

                <article class="card-light p-6 md:p-7 reveal">
                    <div class="inline-flex items-center justify-center w-11 h-11 rounded-premium bg-accent-500/10 text-accent-600 mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-ink-900 mb-2">{{ __('app.Benefit transparent title') }}</h3>
                    <p class="text-sm text-ink-500 leading-relaxed">{{ __('app.Benefit transparent body') }}</p>
                </article>

                <article class="card-light p-6 md:p-7 reveal">
                    <div class="inline-flex items-center justify-center w-11 h-11 rounded-premium bg-accent-500/10 text-accent-600 mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-ink-900 mb-2">{{ __('app.Benefit simple title') }}</h3>
                    <p class="text-sm text-ink-500 leading-relaxed">{{ __('app.Benefit simple body') }}</p>
                </article>

                <article class="card-light p-6 md:p-7 reveal">
                    <div class="inline-flex items-center justify-center w-11 h-11 rounded-premium bg-accent-500/10 text-accent-600 mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 0 1 8.413 3.488 11.824 11.824 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-ink-900 mb-2">{{ __('app.Benefit whatsapp title') }}</h3>
                    <p class="text-sm text-ink-500 leading-relaxed">{{ __('app.Benefit whatsapp body') }}</p>
                </article>
            </div>
        </div>
    </section>

    {{-- ============================================================
         RENTAL SERVICES
         ============================================================ --}}
    @php
        $services = [
            ['key' => 'daily',      'icon' => 'sun'],
            ['key' => 'weekly',     'icon' => 'calendar-week'],
            ['key' => 'monthly',    'icon' => 'calendar-month'],
            ['key' => 'self drive', 'icon' => 'key'],
            ['key' => 'with driver','icon' => 'user'],
            ['key' => 'business',   'icon' => 'briefcase'],
            ['key' => 'holiday',    'icon' => 'palm'],
        ];
        $serviceIcons = [
            'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>',
            'calendar-week' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
            'calendar-month' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
            'key' => '<path d="m21 2-9 9"/><path d="m15.5 7.5 3 3"/><path d="M18 13 4.5 26.5"/><path d="m2 23 6-6"/><circle cx="7.5" cy="16.5" r="3.5"/>',
            'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
            'palm' => '<path d="M12 22V8"/><path d="M5 12c0-2 1-4 3-6"/><path d="M19 12c0-2-1-4-3-6"/><path d="M8 22c-3-1-6-4-6-9 4 0 7 2 9 6"/><path d="M16 22c3-1 6-4 6-9-4 0-7 2-9 6"/>',
        ];
    @endphp

    <section id="layanan" class="section-ink">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="max-w-2xl mb-12 md:mb-16 reveal">
                <span class="eyebrow-on-dark">{{ __('app.Services') }}</span>
                <h2 class="display-lg text-bone-50 mt-4">{{ __('app.Services title') }}</h2>
                <p class="mt-4 text-base md:text-lg text-bone-50/70 leading-relaxed">{{ __('app.Services subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6">
                @foreach ($services as $svc)
                    @php $iconPath = $serviceIcons[$svc['icon']] ?? ''; @endphp
                    <article class="card-dark p-6 md:p-7 flex flex-col reveal">
                        <div class="inline-flex items-center justify-center w-11 h-11 rounded-premium bg-bone-50/[0.05] text-accent-400 mb-5 border border-bone-50/[0.06]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $iconPath !!}</svg>
                        </div>
                        <h3 class="font-display text-lg font-semibold text-bone-50 mb-2">{{ __('app.Service ' . $svc['key'] . ' title') }}</h3>
                        <p class="text-sm text-bone-50/65 leading-relaxed mb-5 flex-1">{{ __('app.Service ' . $svc['key'] . ' body') }}</p>
                        <x-whatsapp-cta
                            variant="ghost-light"
                            class="self-start !px-4 !py-2 !text-xs"
                            :message="'Halo, saya ingin bertanya tentang layanan ' . __('app.Service ' . $svc['key'] . ' title') . '.'"
                            :label="__('app.Ask on whatsapp')"
                        />
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================
         HOW IT WORKS
         ============================================================ --}}
    <section id="cara-sewa" class="section-bone">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="max-w-2xl mb-12 md:mb-16 reveal">
                <span class="eyebrow">{{ __('app.How it works') }}</span>
                <h2 class="display-lg text-ink-900 mt-4">{{ __('app.How it works title') }}</h2>
                <p class="mt-4 text-base md:text-lg text-ink-500 leading-relaxed">{{ __('app.How it works subtitle') }}</p>
            </div>

            <ol class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                @for ($i = 1; $i <= 3; $i++)
                    <li class="relative card-light p-7 md:p-8 reveal">
                        <span class="absolute -top-3 left-7 inline-flex items-center justify-center w-9 h-9 rounded-full bg-ink-950 text-bone-50 font-display text-sm font-bold shadow-ink-glow">
                            0{{ $i }}
                        </span>
                        <h3 class="font-display text-xl font-semibold text-ink-900 mt-3 mb-3">{{ __('app.Step ' . $i . ' title') }}</h3>
                        <p class="text-sm md:text-base text-ink-500 leading-relaxed">{{ __('app.Step ' . $i . ' body') }}</p>
                    </li>
                @endfor
            </ol>

            <div class="mt-12 text-center reveal">
                <x-whatsapp-cta :label="__('app.Chat on WhatsApp')" />
            </div>
        </div>
    </section>

    {{-- ============================================================
         FEATURED VEHICLE SPOTLIGHT
         ============================================================ --}}
    @if ($spotlight)
        @php
            $spotSeats = $spotlight->spec('seats');
            $spotTrans = $spotlight->spec('transmission');
            $spotFuel  = $spotlight->spec('fuel');
            $spotTransLabel = match (true) {
                str_contains((string) $spotTrans, 'auto') || $spotTrans === 'automatic' => 'Automatic',
                default => 'Manual',
            };
            $spotFuelLabel = match ($spotFuel) {
                'bensin'  => 'Bensin',
                'diesel'  => 'Diesel',
                'hybrid'  => 'Hybrid',
                'listrik' => 'Listrik',
                default   => $spotFuel,
            };
            $spotMessage = "Halo, saya ingin menanyakan ketersediaan {$spotlight->shortName()} untuk tanggal ____ sampai ____.";
        @endphp
        <section class="section-ink-soft">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                    <div class="order-2 lg:order-1 reveal">
                        <span class="eyebrow-on-dark">{{ __('app.Spotlight title') }}</span>
                        <h2 class="display-lg text-bone-50 mt-4 mb-3">{{ $spotlight->displayName() }}</h2>
                        <p class="text-base md:text-lg text-bone-50/70 leading-relaxed mb-8 max-w-xl">
                            {{ __('app.Spotlight subtitle') }}
                        </p>

                        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                            @if ($spotSeats)
                                <div class="rounded-premium border border-bone-50/[0.06] bg-ink-900/50 px-4 py-3">
                                    <dt class="text-[11px] uppercase tracking-[0.15em] text-bone-50/50 mb-1">{{ __('app.Spotlight feature seats') }}</dt>
                                    <dd class="font-display text-lg font-semibold text-bone-50">{{ $spotSeats }}</dd>
                                </div>
                            @endif
                            <div class="rounded-premium border border-bone-50/[0.06] bg-ink-900/50 px-4 py-3">
                                <dt class="text-[11px] uppercase tracking-[0.15em] text-bone-50/50 mb-1">{{ __('app.Spotlight feature transmission') }}</dt>
                                <dd class="font-display text-lg font-semibold text-bone-50">{{ $spotTransLabel }}</dd>
                            </div>
                            @if ($spotFuelLabel)
                                <div class="rounded-premium border border-bone-50/[0.06] bg-ink-900/50 px-4 py-3">
                                    <dt class="text-[11px] uppercase tracking-[0.15em] text-bone-50/50 mb-1">{{ __('app.Spotlight feature fuel') }}</dt>
                                    <dd class="font-display text-lg font-semibold text-bone-50">{{ $spotFuelLabel }}</dd>
                                </div>
                            @endif
                            <div class="rounded-premium border border-bone-50/[0.06] bg-ink-900/50 px-4 py-3">
                                <dt class="text-[11px] uppercase tracking-[0.15em] text-bone-50/50 mb-1">{{ __('app.Spotlight feature interior') }}</dt>
                                <dd class="font-display text-lg font-semibold text-bone-50">✓</dd>
                            </div>
                        </dl>

                        <div class="mb-8">
                            <p class="text-xs uppercase tracking-[0.15em] text-bone-50/50 mb-1">{{ __('app.Spotlight price from') }}</p>
                            <p class="font-display text-3xl md:text-4xl font-bold text-bone-50">
                                @rupiah($spotlight->daily_rate)
                                <span class="text-base font-normal text-bone-50/60">{{ __('app.Spotlight per day') }}</span>
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <x-whatsapp-cta :message="$spotMessage" :label="__('app.Check availability')" />
                            <a href="{{ route('vehicles.show', $spotlight) }}" class="btn-ghost-light">
                                {{ __('app.View details') }}
                            </a>
                        </div>
                    </div>

                    <div class="order-1 lg:order-2 reveal">
                        <div class="relative aspect-[4/3] rounded-premium-lg bg-gradient-to-br from-ink-800 to-ink-950 border border-bone-50/[0.06] shadow-premium-lg overflow-hidden">
                            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-accent-500/[0.08] to-transparent"></div>
                            <div class="absolute inset-0 flex items-center justify-center px-8 pb-8">
                                {{-- <x-vehicle-silhouette :type="$spotlight->type->value" variant="dark" /> --}}
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($spotlight->photos[0]) }}" alt="{{ $spotlight->displayName() }}" loading="lazy" style="object-fit: contain!important;">
                            </div>
                            <div class="absolute top-5 left-5 inline-flex items-center gap-2 rounded-full bg-ink-950/70 backdrop-blur-sm border border-bone-50/10 px-3 py-1.5 text-xs font-medium text-bone-50">
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-accent-500 animate-pulse"></span>
                                {{ __('app.Available') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================================
         CUSTOMER TESTIMONIALS
         ============================================================ --}}
    <section class="section-bone">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="max-w-2xl mb-12 md:mb-16 reveal">
                <span class="eyebrow">{{ __('app.Testimonials title') }}</span>
                <h2 class="display-lg text-ink-900 mt-4">{{ __('app.Testimonials title') }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                @for ($i = 1; $i <= 3; $i++)
                    <figure class="card-light p-7 md:p-8 flex flex-col reveal">
                        <div class="flex items-center gap-1 text-accent-500 mb-5" aria-label="5 out of 5 stars">
                            @for ($s = 0; $s < 5; $s++)
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            @endfor
                        </div>
                        <blockquote class="font-display text-base md:text-lg leading-relaxed text-ink-900 flex-1">
                            "{{ __('app.Testimonial ' . $i . ' body') }}"
                        </blockquote>
                        <figcaption class="mt-6 pt-5 border-t border-ink-900/[0.06] flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-ink-950 text-bone-50 font-display text-sm font-semibold">
                                {{ chr(64) }}{{ $i }}
                            </span>
                            <div>
                                <p class="text-sm font-medium text-ink-900">{{ __('app.Testimonial attribution') }} #{{ $i }}</p>
                                <p class="text-xs text-ink-500">Verified rental</p>
                            </div>
                        </figcaption>
                    </figure>
                @endfor
            </div>
        </div>
    </section>

    {{-- ============================================================
         SERVICE AREA
         ============================================================ --}}
    <section id="kontak" class="section-bone-soft">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                <div class="reveal">
                    <span class="eyebrow">{{ __('app.Service area title') }}</span>
                    <h2 class="display-lg text-ink-900 mt-4">{{ __('app.Service area title') }}</h2>
                    <p class="mt-4 text-base md:text-lg text-ink-500 leading-relaxed">
                        {{ __('app.Service area subtitle') }}
                    </p>

                    <div class="mt-8">
                        <x-whatsapp-cta :label="__('app.Contact us')" />
                    </div>
                </div>

                <div class="reveal">
                    <div class="card-light p-6 md:p-8">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 md:gap-4">
                            @foreach (['Jakarta', 'Jakarta Selatan', 'Tangerang', 'Bekasi', 'Depok', 'Bogor'] as $area)
                                <div class="flex items-center gap-2.5 rounded-premium bg-bone-100 border border-ink-900/[0.04] px-4 py-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-accent-600 shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span class="text-sm font-medium text-ink-900">{{ $area }}</span>
                                </div>
                            @endforeach
                        </div>

                        @if ($branch && ($branch->address || $branch->map_url))
                            <div class="mt-6 pt-6 border-t border-ink-900/[0.06]">
                                <p class="text-xs uppercase tracking-[0.15em] text-ink-400 mb-2">{{ __('app.Address') }}</p>
                                @if ($branch->address)
                                    <p class="text-sm text-ink-700 mb-3">{{ $branch->address }}</p>
                                @endif
                                @if ($branch->map_url)
                                    <a href="{{ $branch->map_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-sm font-medium text-accent-600 hover:text-accent-700 transition-colors duration-150">
                                        Lihat di peta
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         FAQ
         ============================================================ --}}
    <section id="faq" class="section-bone">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="text-center max-w-2xl mx-auto mb-12 md:mb-16 reveal">
                <span class="eyebrow justify-center">{{ __('app.FAQ') }}</span>
                <h2 class="display-lg text-ink-900 mt-4">{{ __('app.FAQ') }}</h2>
            </div>

            <div class="reveal">
                @for ($i = 1; $i <= 9; $i++)
                    <details class="faq-item">
                        <summary>{{ __('app.Faq q' . $i) }}</summary>
                        <div class="faq-answer">{{ __('app.Faq a' . $i) }}</div>
                    </details>
                @endfor
            </div>

            <div class="mt-12 text-center reveal">
                <p class="text-sm text-ink-500 mb-4">Pertanyaan lain? Tim kami siap membantu.</p>
                <x-whatsapp-cta :label="__('app.Chat on WhatsApp')" />
            </div>
        </div>
    </section>

    {{-- ============================================================
         FINAL CTA
         ============================================================ --}}
    <section class="section-ink relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[400px] w-[400px] rounded-full bg-accent-500/[0.08] blur-3xl"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 text-center reveal">
            <span class="eyebrow-on-dark justify-center">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-accent-500"></span>
                {{ __('app.Chat on WhatsApp') }}
            </span>

            <h2 class="display-lg text-bone-50 mt-5">{{ __('app.Final cta title') }}</h2>
            <p class="mt-5 text-base md:text-xl text-bone-50/70 max-w-2xl mx-auto leading-relaxed">
                {{ __('app.Final cta subtitle') }}
            </p>

            <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
                <x-whatsapp-cta :label="__('app.Chat on WhatsApp')" />
                @if ($branch && $branch->phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $branch->phone) }}" class="btn-ghost-light">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        {{ $branch->phone }}
                    </a>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    @php
        $fabRaw = config('business.whatsapp_number') ?? config('business.business.phone', '+62 812-3456-7890');
        $fabDigits = preg_replace('/\D+/', '', $fabRaw);
        if (str_starts_with($fabDigits, '0')) {
            $fabDigits = '62' . substr($fabDigits, 1);
        }
        $fabText = __('app.Chat WhatsApp');
        $fabUrl = 'https://wa.me/' . $fabDigits . '?text=' . urlencode($fabText);
    @endphp

    {{-- Floating WhatsApp button for mobile --}}
    <a href="{{ $fabUrl }}" id="fab-whatsapp" class="fab-whatsapp" target="_blank" rel="noopener" aria-label="{{ __('app.Chat on WhatsApp') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 0 1 8.413 3.488 11.824 11.824 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 0 0 1.51 5.26l-.999 3.648 3.978-.607zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/>
        </svg>
    </a>

    <script>
        // Reveal-on-scroll for elements with the .reveal class.
        (function () {
            var items = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window) || items.length === 0) {
                items.forEach(function (el) { el.classList.add('is-visible'); });
                return;
            }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });
            items.forEach(function (el) { io.observe(el); });
        })();

        // Promo slider. Auto-advances every 6s, pauses on hover or when the
        // tab is hidden, supports keyboard arrows and touch swipes.
        // `slidesData` is passed in from Blade and carries each slide's
        // id, image url, alt text, and optional CTA label/url.
        window.promoSlider = function (slidesData) {
            var slides = Array.isArray(slidesData) ? slidesData : [];
            var count = slides.length;
            return {
                slides: slides,
                active: 0,
                paused: false,
                timer: null,
                touchStartX: 0,
                touchEndX: 0,
                DELAY: 6000,

                init: function () {
                    if (count <= 1) return;
                    var self = this;
                    this.start();
                    document.addEventListener('visibilitychange', function () {
                        if (document.hidden) {
                            self.stop();
                        } else if (!self.paused) {
                            self.start();
                        }
                    });
                },

                start: function () {
                    this.stop();
                    if (count <= 1) return;
                    var self = this;
                    this.timer = setInterval(function () {
                        if (!self.paused && !document.hidden) {
                            self.active = (self.active + 1) % count;
                        }
                    }, this.DELAY);
                },

                stop: function () {
                    if (this.timer) {
                        clearInterval(this.timer);
                        this.timer = null;
                    }
                },

                goTo: function (i) {
                    if (i < 0 || i >= count) return;
                    this.active = i;
                    this.start();
                },

                next: function () {
                    if (count === 0) return;
                    this.active = (this.active + 1) % count;
                    this.start();
                },

                prev: function () {
                    if (count === 0) return;
                    this.active = (this.active - 1 + count) % count;
                    this.start();
                },

                handleSwipe: function () {
                    var dx = this.touchEndX - this.touchStartX;
                    if (Math.abs(dx) < 40) return;
                    if (dx < 0) this.next();
                    else this.prev();
                },
            };
        };

        // Hero availability form. Registers as a global Alpine data factory
        // so `x-data="availabilityForm(...)"` resolves it. Pre-fills nothing
        // by default — the visitor picks location, dates, and unit themselves
        // and sees a live cost estimate. All copy comes from data-* attributes
        // on the form element (set by Blade) so the JS stays Blade-free.
        (function () {
            var costFmt = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            });
            var dateFmt = new Intl.DateTimeFormat('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            });

            function todayIso() {
                var d = new Date();
                var pad = function (n) { return n < 10 ? '0' + n : '' + n; };
                return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
            }

            function attr(el, name) {
                return el.getAttribute(name) || '';
            }

            window.availabilityForm = function (units, defaultRate) {
                return {
                    location: '',
                    pickup: '',
                    ret: '',
                    unitId: '',
                    units: units || [],
                    today: todayIso(),
                    error: '',

                    init: function () {
                        this.pickup = this.today;
                    },

                    get selectedUnit() {
                        if (!this.unitId) return null;
                        for (var i = 0; i < this.units.length; i++) {
                            if (String(this.units[i].id) === String(this.unitId)) return this.units[i];
                        }
                        return null;
                    },

                    get rate() {
                        var u = this.selectedUnit;
                        return u ? u.rate : (defaultRate || 0);
                    },

                    get rateLabel() {
                        if (!this.rate) return '';
                        var u = this.selectedUnit;
                        var root = this.$root;
                        var fromLabel = attr(root, 'data-label-from');
                        var perDay = attr(root, 'data-per-day');
                        var name = u ? u.name : fromLabel;
                        return name + ' · ' + costFmt.format(this.rate) + ' ' + perDay;
                    },

                    get dayCount() {
                        if (!this.pickup || !this.ret) return null;
                        var p = new Date(this.pickup + 'T00:00:00');
                        var r = new Date(this.ret + 'T00:00:00');
                        var diff = Math.round((r - p) / 86400000);
                        return diff < 0 ? null : diff;
                    },

                    get daysLabel() {
                        var d = this.dayCount;
                        if (d === null || this.rate === 0) return '';
                        var root = this.$root;
                        var halfDay = attr(root, 'data-half-day');
                        var daySuffix = attr(root, 'data-days-suffix');
                        if (d === 0) return '· ' + halfDay;
                        var word = (d === 1 ? '1 ' : d + ' ') + daySuffix;
                        return '· ' + word;
                    },

                    get cost() {
                        if (!this.rate) return 0;
                        var d = this.dayCount;
                        if (d === null) return 0;
                        if (d === 0) return Math.round(this.rate / 2);
                        return this.rate * d;
                    },

                    get formattedCost() {
                        return costFmt.format(this.cost);
                    },

                    get canSubmit() {
                        return this.location && this.pickup && this.ret
                            && this.dayCount !== null && !this.error;
                    },

                    validate: function () {
                        this.error = '';
                        if (!this.pickup) return;
                        var root = this.$root;
                        if (this.pickup < this.today) {
                            this.error = attr(root, 'data-past');
                            return;
                        }
                        if (this.ret && this.ret < this.pickup) {
                            this.error = attr(root, 'data-invalid');
                        }
                    },

                    submit: function () {
                        this.validate();
                        if (this.error) return;
                        if (!this.canSubmit) return;

                        var root = this.$root;
                        var start = dateFmt.format(new Date(this.pickup + 'T00:00:00'));
                        var end = dateFmt.format(new Date(this.ret + 'T00:00:00'));
                        var costStr = costFmt.format(this.cost);
                        var loc = this.location;
                        var unit = this.selectedUnit;
                        var tpl = unit
                            ? attr(root, 'data-tpl-unit')
                            : attr(root, 'data-tpl-any');
                        var unitName = unit ? unit.name : '';

                        // Square-bracket tokens so they never collide with
                        // Blade, JS, or TypeScript syntax.
                        var tokens = {
                            '[unit]': unitName,
                            '[start]': start,
                            '[end]': end,
                            '[location]': loc,
                            '[cost]': costStr,
                            '[count]': String(this.dayCount),
                        };
                        var text = tpl;
                        for (var k in tokens) {
                            text = text.split(k).join(tokens[k]);
                        }

                        var waPhone = attr(root, 'data-phone');
                        var url = 'https://wa.me/' + waPhone + '?text=' + encodeURIComponent(text);
                        window.open(url, '_blank', 'noopener');
                    },
                };
            };
        })();
    </script>
@endpush
