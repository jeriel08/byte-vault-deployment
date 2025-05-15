@props(['href', 'active' => false])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'nav-link' . ($active ? ' active' : '')]) }}>
    {{ $slot }}
</a>