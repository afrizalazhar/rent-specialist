@extends('layouts.app')

@section('title', $vehicle->displayName() . ' — ' . config('business.business.name', 'Rent Specialist'))

@section('content')
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 md:pt-16 pb-16">
        <div class="mb-6">
            <a href="{{ route('vehicles.index') }}" class="text-sm text-surface-500 hover:text-clay-500 transition-colors duration-150">
                &larr; Kembali ke katalog
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-10">
            <div class="lg:col-span-3">
                <div class="neu-frame p-2 mb-4">
                    @if (!empty($vehicle->photos[0]))
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($vehicle->photos[0]) }}" alt="{{ $vehicle->displayName() }}" class="w-full aspect-video object-cover rounded-neu">
                    @else
                        <div class="w-full aspect-video rounded-neu bg-surface-300 flex items-center justify-center">
                            <span class="font-display text-5xl font-bold text-surface-500">{{ strtoupper(substr($vehicle->make, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>

                @if (count($vehicle->photos) > 1)
                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-3">
                        @foreach (array_slice($vehicle->photos, 1) as $photo)
                            <div class="neu-frame p-1">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($photo) }}" alt="{{ $vehicle->displayName() }}" class="w-full aspect-square object-cover rounded-neu-sm" loading="lazy">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="neu-surface p-6">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h1 class="font-display text-2xl md:text-3xl font-bold text-surface-800 tracking-tight">{{ $vehicle->displayName() }}</h1>
                        <x-neu-pill variant="success">{{ $vehicle->status->label() }}</x-neu-pill>
                    </div>
                    <p class="text-surface-500 mb-6">{{ $vehicle->type->labelId() }}</p>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-surface-300/50">
                            <span class="text-sm text-surface-600">{{ __('app.Daily rate') }}</span>
                            <span class="font-display font-semibold text-surface-800">@rupiah($vehicle->daily_rate)</span>
                        </div>
                        @if ($vehicle->weekly_rate)
                            <div class="flex items-center justify-between py-2 border-b border-surface-300/50">
                                <span class="text-sm text-surface-600">{{ __('app.Weekly rate') }}</span>
                                <span class="font-display font-semibold text-surface-800">@rupiah($vehicle->weekly_rate)</span>
                            </div>
                        @endif
                        @if ($vehicle->monthly_rate)
                            <div class="flex items-center justify-between py-2 border-b border-surface-300/50">
                                <span class="text-sm text-surface-600">{{ __('app.Monthly rate') }}</span>
                                <span class="font-display font-semibold text-surface-800">@rupiah($vehicle->monthly_rate)</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6">
                        <x-whatsapp-cta
                            class="w-full justify-center"
                            :message="'Halo, saya tertarik dengan ' . $vehicle->shortName() . ' untuk tanggal ____ sampai ____. Mohon info ketersediaannya.'"
                            :label="__('app.Chat on WhatsApp')"
                        />
                    </div>
                </div>

                <div class="neu-surface p-6">
                    <h2 class="font-display text-lg font-semibold text-surface-800 mb-4">{{ __('app.Availability') }}</h2>
                    <livewire:availability-calendar :vehicle="$vehicle" />
                </div>
            </div>
        </div>

        <div class="mt-10">
            <x-neu-card title="{{ __('app.Specifications') }}">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div class="neu-inset-sm p-4">
                        <p class="text-xs text-surface-500 mb-1">Merek</p>
                        <p class="font-medium text-surface-800">{{ $vehicle->make }}</p>
                    </div>
                    <div class="neu-inset-sm p-4">
                        <p class="text-xs text-surface-500 mb-1">Model</p>
                        <p class="font-medium text-surface-800">{{ $vehicle->model }}</p>
                    </div>
                    <div class="neu-inset-sm p-4">
                        <p class="text-xs text-surface-500 mb-1">Tahun</p>
                        <p class="font-medium text-surface-800">{{ $vehicle->year }}</p>
                    </div>
                    <div class="neu-inset-sm p-4">
                        <p class="text-xs text-surface-500 mb-1">Warna</p>
                        <p class="font-medium text-surface-800">{{ $vehicle->color ?? '-' }}</p>
                    </div>

                    @foreach ($vehicle->type->specFields() as $key => $field)
                        @php
                        $value = $vehicle->spec($key);
                        @endphp
                        <div class="neu-inset-sm p-4">
                            <p class="text-xs text-surface-500 mb-1">{{ $field['label'] }}</p>
                            <p class="font-medium text-surface-800">
                                @if ($field['type'] === 'boolean')
                                    {{ $value ? 'Ya' : 'Tidak' }}
                                @elseif (is_null($value))
                                    -
                                @else
                                    {{ $value }}{{ $field['unit'] ? ' ' . $field['unit'] : '' }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            </x-neu-card>
        </div>

        <div class="mt-10 neu-inset p-8 text-center">
            <h2 class="font-display text-xl font-semibold text-surface-800 mb-3">Tertarik dengan kendaraan ini?</h2>
            <p class="text-surface-600 mb-6 max-w-xl mx-auto">Kirim pesan WhatsApp dengan menyebutkan tanggal mulai dan selesai sewa. Kami akan membalas secepatnya.</p>
            <x-whatsapp-cta
                :message="'Halo, saya tertarik dengan ' . $vehicle->shortName() . ' untuk tanggal ____ sampai ____. Mohon info ketersediaannya.'"
                :label="__('app.Send WhatsApp message')"
            />
        </div>
    </section>
@endsection
