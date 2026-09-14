@extends('layouts.app')

@section('title', __('app.Contact') . ' — ' . config('business.business.name', 'Rent Specialist'))

@section('content')
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 md:pt-20 pb-16">
        <div class="max-w-2xl mb-10">
            <h1 class="font-display text-3xl md:text-4xl font-bold text-surface-800 tracking-tight mb-3">{{ __('app.Contact') }}</h1>
            <p class="text-surface-600">Silakan hubungi kami untuk pertanyaan, cek ketersediaan, atau pemesanan.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <x-neu-card title="Detail kontak">
                    <dl class="space-y-5">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 w-10 h-10 rounded-neu-sm bg-surface-100 shadow-neu-sm flex items-center justify-center text-clay-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-surface-500 mb-1">{{ __('app.Address') }}</dt>
                                <dd class="text-surface-800">{{ $branch?->address ?? config('business.business.address', 'Jl. Contoh No. 123, Jakarta') }}</dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="shrink-0 w-10 h-10 rounded-neu-sm bg-surface-100 shadow-neu-sm flex items-center justify-center text-clay-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-surface-500 mb-1">{{ __('app.Phone') }}</dt>
                                <dd class="text-surface-800">{{ $branch?->phone ?? config('business.business.phone', '+62 812-3456-7890') }}</dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="shrink-0 w-10 h-10 rounded-neu-sm bg-surface-100 shadow-neu-sm flex items-center justify-center text-clay-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-surface-500 mb-1">{{ __('app.Email') }}</dt>
                                <dd class="text-surface-800">{{ $branch?->email ?? config('business.business.email', 'halo@rent.local') }}</dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="shrink-0 w-10 h-10 rounded-neu-sm bg-surface-100 shadow-neu-sm flex items-center justify-center text-clay-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-surface-500 mb-1">{{ __('app.Hours') }}</dt>
                                <dd class="text-surface-800">{{ $branch?->hours ?? config('business.business.hours', 'Senin–Minggu, 08.00–21.00') }}</dd>
                            </div>
                        </div>
                    </dl>

                    <x-slot:footer>
                        <x-whatsapp-cta class="w-full justify-center" :label="__('app.Chat on WhatsApp')" />
                    </x-slot:footer>
                </x-neu-card>

                <x-neu-card title="Lokasi">
                    <p class="text-sm text-surface-600 mb-4">Klik tautan di bawah untuk melihat lokasi kami di peta.</p>
                    <x-neu-button href="{{ $branch?->map_url ?? config('business.business.map_url', 'https://maps.google.com/?q=-6.200,106.816') }}" target="_blank" rel="noopener">
                        Buka di Google Maps
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                    </x-neu-button>
                </x-neu-card>
            </div>

            <div class="lg:col-span-1">
                <div class="neu-inset p-6 sticky top-24">
                    <h2 class="font-display text-lg font-semibold text-surface-800 mb-3">Pesan via WhatsApp</h2>
                    <p class="text-sm text-surface-600 leading-relaxed mb-5">Kirim pesan untuk cek ketersediaan kendaraan dan tanggal yang Anda inginkan.</p>
                    <x-whatsapp-cta class="w-full justify-center" :label="__('app.Send WhatsApp message')" />
                </div>
            </div>
        </div>
    </section>
@endsection
