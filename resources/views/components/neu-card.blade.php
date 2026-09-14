@props(['title' => null])

<div {{ $attributes->merge(['class' => 'neu-surface p-6 md:p-8']) }}>
    @if ($title)
        <div class="mb-5">
            <h3 class="font-display text-xl font-semibold text-surface-800">{{ $title }}</h3>
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="mt-5 pt-5 border-t border-surface-300/60">
            {{ $footer }}
        </div>
    @endif
</div>
