@props(['align' => 'right', 'width' => '48'])

@php
    $alignmentClasses = match ($align) {
        'left' => 'dropdown-menu-start',
        'top' => '', // Bootstrap doesn’t natively support "top" alignment this way; we’ll assume default
        default => 'dropdown-menu-end', // Right-aligned
    };

    // Bootstrap doesn’t use Tailwind’s 'w-48' (12rem); we’ll set width via CSS if needed
    $width = match ($width) {
        '48' => '12rem', // Matches Tailwind’s w-48
        default => $width,
    };
@endphp

<div class="dropdown">
    <!-- Trigger -->
    <div class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        {{ $trigger }}
    </div>

    <!-- Dropdown Content -->
    <div class="dropdown-menu {{ $alignmentClasses }}" aria-labelledby="dropdownMenuButton" style="width: {{ $width }};">
        {{ $content }}
    </div>
</div>