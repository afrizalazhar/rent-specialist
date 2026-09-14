@props(['variant' => 'neutral'])

@php
$pillClass = match ($variant) {
    'success' => 'neu-pill-success',
    'warning' => 'neu-pill-warning',
    'danger' => 'neu-pill-danger',
    default => 'neu-pill-neutral',
};
@endphp

<span {{ $attributes->merge(['class' => 'neu-pill ' . $pillClass]) }}>
    {{ $slot }}
</span>
