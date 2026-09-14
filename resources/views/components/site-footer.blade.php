@php
    $business = config('business.business', []);
    $name     = config('business.business.name', 'Rent Specialist');
    $phone    = $business['phone']    ?? '+62 812-3456-7890';
    $email    = $business['email']    ?? 'halo@rent.local';
    $address  = $business['address']  ?? 'Jl. Contoh No. 123, Jakarta';
    $hours    = $business['hours']    ?? 'Senin–Minggu, 08.00–21.00';
    $mapUrl   = $business['map_url']  ?? null;

    $onHome = request()->routeIs('home');
    $sectionHref = function (string $hash, ?string $fallbackRoute = null) use ($onHome) {
        if ($onHome) return '#' . $hash;
        return ($fallbackRoute ? route($fallbackRoute) : route('home')) . '#' . $hash;
    };
@endphp

<footer class="bg-ink-950 text-bone-50 mt-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 md:pt-20 pb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 md:gap-12">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 mb-4">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-bone-50 text-ink-950 font-display font-bold text-sm">
                        {{ strtoupper(substr($name, 0, 1)) }}
                    </span>
                    <span class="font-display text-lg font-semibold tracking-tight text-bone-50">
                        {{ $name }}
                    </span>
                </a>
                <p class="text-sm leading-relaxed text-bone-50/70 max-w-xs">
                    {{ __('app.Footer brand description') }}
                </p>
            </div>

            <div>
                <h4 class="font-display text-sm font-semibold uppercase tracking-[0.18em] text-bone-50/90 mb-4">
                    {{ __('app.Footer company') }}
                </h4>
                <ul class="space-y-2.5 text-sm text-bone-50/70">
                    <li><a href="{{ $sectionHref('tentang', 'pages.about') }}" class="hover:text-accent-400 transition-colors duration-150">{{ __('app.Footer about') }}</a></li>
                    <li><a href="{{ $sectionHref('armada', 'vehicles.index') }}" class="hover:text-accent-400 transition-colors duration-150">{{ __('app.Footer fleet') }}</a></li>
                    <li><a href="{{ $sectionHref('layanan') }}" class="hover:text-accent-400 transition-colors duration-150">{{ __('app.Footer services') }}</a></li>
                    <li><a href="{{ $sectionHref('mengapa-kami') }}" class="hover:text-accent-400 transition-colors duration-150">{{ __('app.Footer why us') }}</a></li>
                    <li><a href="{{ $sectionHref('faq') }}" class="hover:text-accent-400 transition-colors duration-150">{{ __('app.Footer faq') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-display text-sm font-semibold uppercase tracking-[0.18em] text-bone-50/90 mb-4">
                    {{ __('app.Footer contact') }}
                </h4>
                <ul class="space-y-3 text-sm text-bone-50/70">
                    <li class="flex items-start gap-3">
                        <svg class="shrink-0 mt-0.5 text-accent-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 0 1 8.413 3.488 11.824 11.824 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24z"/>
                        </svg>
                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', config('business.whatsapp_number') ?? $phone) }}" target="_blank" rel="noopener" class="hover:text-accent-400 transition-colors duration-150">{{ __('app.Footer whatsapp') }}</a>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="shrink-0 mt-0.5 text-accent-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                        <a href="#" class="hover:text-accent-400 transition-colors duration-150">{{ __('app.Footer instagram') }}</a>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="shrink-0 mt-0.5 text-accent-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <span>{{ $phone }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="shrink-0 mt-0.5 text-accent-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>{{ $address }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="shrink-0 mt-0.5 text-accent-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>{{ $hours }}</span>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="font-display text-sm font-semibold uppercase tracking-[0.18em] text-bone-50/90 mb-4">
                    {{ __('app.Footer service areas') }}
                </h4>
                <ul class="space-y-2.5 text-sm text-bone-50/70">
                    <li>Jakarta</li>
                    <li>Jakarta Selatan</li>
                    <li>Tangerang</li>
                    <li>Bekasi</li>
                    <li>Depok</li>
                    <li>Bogor</li>
                </ul>
                @if ($mapUrl)
                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 mt-4 text-sm font-medium text-accent-400 hover:text-accent-300 transition-colors duration-150">
                        Lihat di peta
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-14 pt-8 border-t border-bone-50/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-xs text-bone-50/50">
                &copy; {{ date('Y') }} {{ $name }}. {{ __('app.Footer copyright') }}
            </p>
            <x-whatsapp-cta class="!px-4 !py-2 !text-xs" :label="__('app.Chat on WhatsApp')" />
        </div>
    </div>
</footer>
