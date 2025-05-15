@props(['href', 'active' => false])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'responsive-nav-link' . ($active ? ' active' : '')]) }}>
    {{ $slot }}
</a>