@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => 'secondary-button d-flex align-items-center justify-content-center gap-2']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => 'secondary-button d-flex align-items-center justify-content-center gap-2']) }}>
        {{ $slot }}
    </button>
@endif