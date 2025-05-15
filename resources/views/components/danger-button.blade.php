<button {{ $attributes->merge(['type' => 'submit', 'class' => 'danger-button d-flex align-items-center justify-content-center gap-2']) }}>
    {{ $slot }}
</button>
