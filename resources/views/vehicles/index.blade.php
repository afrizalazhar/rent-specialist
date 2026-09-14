@extends('layouts.app')

@section('title', __('app.Vehicles') . ' — ' . config('business.business.name', 'Rent Specialist'))

@section('content')
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 md:pt-16 pb-8">
        <div class="max-w-2xl mb-8">
            <h1 class="font-display text-3xl md:text-4xl font-bold text-surface-800 tracking-tight mb-3">{{ __('app.Vehicles') }}</h1>
            <p class="text-surface-600">Lihat katalog lengkap mobil, SUV, dan sepeda motor kami.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-8">
            <a href="{{ route('vehicles.index') }}" class="neu-pill {{ is_null($activeType) ? 'neu-pill-success' : 'neu-pill-neutral' }}">
                {{ __('app.All') }}
                <span class="ml-1 text-surface-500">{{ array_sum($typeCounts) }}</span>
            </a>

            @foreach (\App\Enums\VehicleType::cases() as $type)
                <a href="{{ route('vehicles.index', ['tipe' => $type->value]) }}" class="neu-pill {{ $activeType === $type ? 'neu-pill-success' : 'neu-pill-neutral' }}">
                    {{ $type->labelId() }}
                    <span class="ml-1 text-surface-500">{{ $typeCounts[$type->value] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        @if ($vehicles->isEmpty())
            <div class="neu-inset p-10 text-center">
                <p class="text-surface-600 mb-6">{{ __('app.No vehicles found') }}.</p>
                <x-whatsapp-cta label="Tanya ketersediaan" />
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @foreach ($vehicles as $vehicle)
                    @include('vehicles._card', ['vehicle' => $vehicle])
                @endforeach
            </div>

            <div class="mt-12">
                {{ $vehicles->links() }}
            </div>
        @endif
    </section>
@endsection
