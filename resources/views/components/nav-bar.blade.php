@props([
    'variant' => 'auto', // auto | light | dark
    // When variant=auto, we look at the document.body data attribute
    // set by the layout so the navbar can match the page surface.
])

@php
    $brand = config('business.business.name', 'Rent Specialist');

    // Sections live on the landing page — anchor links. If we're already on
    // the home page we use "#section", otherwise we link back to the home
    // page with the hash so the visitor lands on the right section.
    $onHome = request()->routeIs('home');
    $anchors = [
        'fleet'        => 'armada',
        'services'     => 'layanan',
        'why-us'       => 'mengapa-kami',
        'how-it-works' => 'cara-sewa',
        'faq'          => 'faq',
        'contact'      => 'kontak',
    ];
    $href = function (string $key, ?string $fallbackRoute = null) use ($anchors, $onHome) {
        $hash = '#' . $anchors[$key];
        if ($onHome) {
            return $hash;
        }
        return $fallbackRoute ? route($fallbackRoute) . $hash : route('home') . $hash;
    };
@endphp

<header
    x-data="{ scrolled: false, open: false, dark: {{ $variant === 'dark' ? 'true' : 'false' }} }"
    x-init="
        scrolled = window.scrollY > 16;
        if ({{ $variant === 'auto' ? 'true' : 'false' }}) {
            dark = document.body.dataset.navTheme === 'dark';
        }
        window.addEventListener('scroll', () => { scrolled = window.scrollY > 16 }, { passive: true });
    "
    :class="dark
        ? (scrolled ? 'nav-bar nav-bar-scrolled' : 'nav-bar nav-bar-default')
        : (scrolled ? 'nav-bar nav-bar-light-scrolled' : 'nav-bar nav-bar-light')"
    class="nav-bar"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <span
                    :class="dark ? 'bg-bone-50 text-ink-950' : 'bg-ink-950 text-bone-50'"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-md font-display font-bold text-sm transition-colors duration-200"
                >
                    {{ strtoupper(substr($brand, 0, 1)) }}
                </span>
                <span
                    :class="dark ? 'text-bone-50' : 'text-ink-900'"
                    class="font-display text-base md:text-lg font-semibold tracking-tight transition-colors duration-200"
                >
                    {{ $brand }}
                </span>
            </a>

            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ $href('fleet', 'vehicles.index') }}" :class="dark ? 'nav-link-on-dark' : 'nav-link-on-light'">
                    {{ __('app.Fleet') }}
                </a>
                <a href="{{ $href('services') }}" :class="dark ? 'nav-link-on-dark' : 'nav-link-on-light'">
                    {{ __('app.Services') }}
                </a>
                <a href="{{ $href('why-us') }}" :class="dark ? 'nav-link-on-dark' : 'nav-link-on-light'">
                    {{ __('app.Why us') }}
                </a>
                <a href="{{ $href('how-it-works') }}" :class="dark ? 'nav-link-on-dark' : 'nav-link-on-light'">
                    {{ __('app.How it works') }}
                </a>
                <a href="{{ $href('faq') }}" :class="dark ? 'nav-link-on-dark' : 'nav-link-on-light'">
                    {{ __('app.FAQ') }}
                </a>
                <a href="{{ $href('contact', 'pages.contact') }}" :class="dark ? 'nav-link-on-dark' : 'nav-link-on-light'">
                    {{ __('app.Contact') }}
                </a>
            </nav>

            <div class="flex items-center gap-2">
                <x-whatsapp-cta
                    class="hidden sm:inline-flex !px-4 !py-2 !text-xs"
                    :label="__('app.Chat on WhatsApp')"
                    icon="true"
                />

                <button
                    type="button"
                    class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-md transition-colors duration-150"
                    :class="dark ? 'text-bone-50 hover:bg-bone-50/10' : 'text-ink-900 hover:bg-ink-900/5'"
                    aria-label="Menu"
                    @click="open = !open"
                >
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                    <svg x-show="open" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden border-t"
        :class="dark ? 'border-bone-50/10 bg-ink-950/95 backdrop-blur-lg' : 'border-ink-900/10 bg-bone-50/95 backdrop-blur-lg'"
        @click.away="open = false"
    >
        <div class="max-w-7xl mx-auto px-4 py-3 space-y-1">
            <a href="{{ $href('fleet', 'vehicles.index') }}" :class="dark ? 'nav-link-on-dark block' : 'nav-link-on-light block'">{{ __('app.Fleet') }}</a>
            <a href="{{ $href('services') }}" :class="dark ? 'nav-link-on-dark block' : 'nav-link-on-light block'">{{ __('app.Services') }}</a>
            <a href="{{ $href('why-us') }}" :class="dark ? 'nav-link-on-dark block' : 'nav-link-on-light block'">{{ __('app.Why us') }}</a>
            <a href="{{ $href('how-it-works') }}" :class="dark ? 'nav-link-on-dark block' : 'nav-link-on-light block'">{{ __('app.How it works') }}</a>
            <a href="{{ $href('faq') }}" :class="dark ? 'nav-link-on-dark block' : 'nav-link-on-light block'">{{ __('app.FAQ') }}</a>
            <a href="{{ $href('contact', 'pages.contact') }}" :class="dark ? 'nav-link-on-dark block' : 'nav-link-on-light block'">{{ __('app.Contact') }}</a>
            <div class="pt-2">
                <x-whatsapp-cta class="w-full !justify-center" :label="__('app.Chat on WhatsApp')" icon="true" />
            </div>
        </div>
    </div>
</header>
