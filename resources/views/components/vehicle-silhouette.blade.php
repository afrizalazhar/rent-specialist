@props([
    'type' => 'car', // car | suv | motorcycle
    'variant' => 'dark', // dark | light
    'class' => 'w-full h-full',
])

@php
    // We render a generic, brand-free vehicle silhouette so the page stays
    // polished even when real photos are missing. The silhouettes are SVG,
    // scalable, and inherit text color.
    $colorClass = $variant === 'dark' ? 'text-bone-50/90' : 'text-ink-900/90';
@endphp

@if ($type === 'motorcycle')
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 280" {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
        <g class="{{ $colorClass }}" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="130" cy="200" r="55" />
            <circle cx="470" cy="200" r="55" />
            <path d="M130 200 L210 200 L260 110 L380 110 L430 200 L470 200" />
            <path d="M260 110 L300 110 L320 80 L380 80 L400 110" />
            <path d="M210 200 L300 200 L330 160 L420 160 L430 200" />
            <line x1="130" y1="200" x2="80" y2="170" />
            <line x1="470" y1="200" x2="510" y2="170" />
        </g>
    </svg>
@elseif ($type === 'suv')
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 280" {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
        <g class="{{ $colorClass }}" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M70 200 L70 160 L110 110 L210 90 L380 90 L470 110 L520 150 L530 200" />
            <path d="M70 200 L530 200 L530 220 L70 220 Z" fill="currentColor" fill-opacity="0.06" />
            <line x1="210" y1="90" x2="220" y2="140" />
            <line x1="380" y1="90" x2="370" y2="140" />
            <line x1="220" y1="140" x2="370" y2="140" />
            <line x1="110" y1="110" x2="210" y2="110" />
            <line x1="380" y1="110" x2="470" y2="110" />
            <circle cx="160" cy="220" r="32" />
            <circle cx="440" cy="220" r="32" />
            <line x1="50" y1="180" x2="90" y2="180" />
        </g>
    </svg>
@else
    {{-- car --}}
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 280" {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
        <g class="{{ $colorClass }}" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M50 200 L70 150 L150 110 L260 100 L420 100 L500 130 L550 150 L560 200" />
            <path d="M50 200 L560 200 L560 220 L50 220 Z" fill="currentColor" fill-opacity="0.06" />
            <line x1="260" y1="100" x2="270" y2="140" />
            <line x1="400" y1="100" x2="390" y2="140" />
            <line x1="270" y1="140" x2="390" y2="140" />
            <line x1="150" y1="110" x2="260" y2="110" />
            <line x1="400" y1="110" x2="500" y2="130" />
            <line x1="150" y1="120" x2="170" y2="140" />
            <line x1="490" y1="135" x2="470" y2="145" />
            <circle cx="170" cy="220" r="32" />
            <circle cx="450" cy="220" r="32" />
            <line x1="30" y1="180" x2="80" y2="180" />
            <line x1="560" y1="180" x2="585" y2="180" />
        </g>
    </svg>
@endif
