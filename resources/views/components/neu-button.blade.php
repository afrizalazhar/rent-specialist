@props([
    'variant' => 'default',
    'href' => null,
    'type' => 'button',
])

@php
$baseClass = $variant === 'primary' ? 'neu-btn-primary' : 'neu-btn';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClass]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $baseClass]) }}>
        {{ $slot }}
    </button>
@endif
