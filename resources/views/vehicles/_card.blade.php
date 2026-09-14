@php
$photo = $vehicle->photos[0] ?? null;
$displayName = $vehicle->displayName();
@endphp

<a href="{{ route('vehicles.show', $vehicle) }}" class="group block neu-surface overflow-hidden hover:shadow-neu-lg transition-shadow duration-150 ease-neu">
    <div class="neu-frame m-3 mb-0 aspect-video">
        @if ($photo)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($photo) }}" alt="{{ $displayName }}" class="w-full h-full object-cover rounded-neu-sm" loading="lazy">
        @else
            <div class="w-full h-full rounded-neu-sm bg-surface-300 flex items-center justify-center">
                <span class="font-display text-3xl font-bold text-surface-500">{{ strtoupper(substr($vehicle->make, 0, 1)) }}</span>
            </div>
        @endif
    </div>

    <div class="p-5">
        <div class="flex items-start justify-between gap-3 mb-3">
            <h3 class="font-display text-lg font-semibold text-surface-800 leading-snug group-hover:text-clay-500 transition-colors duration-150">
                {{ $displayName }}
            </h3>
            @php
            $pillVariant = match ($vehicle->status) {
                \App\Enums\VehicleStatus::Available => 'success',
                \App\Enums\VehicleStatus::OnRent => 'warning',
                \App\Enums\VehicleStatus::OutOfService => 'danger',
                default => 'neutral',
            };
            @endphp
            <x-neu-pill variant="{{ $pillVariant }}">
                {{ $vehicle->status->label() }}
            </x-neu-pill>
        </div>

        <p class="text-sm text-surface-500 mb-4">{{ $vehicle->type->labelId() }}</p>

        <p class="text-sm text-surface-600">
            {{ __('app.Daily rate') }}: <span class="font-semibold text-surface-800">@rupiah($vehicle->daily_rate)</span>
        </p>
    </div>
</a>
