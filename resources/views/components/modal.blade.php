<!-- modal.blade.php -->
@props([
    'name',
    'show' => false,
    'maxWidth' => 'lg'
])

@php
$maxWidth = [
    'sm' => 'modal-sm',
    'md' => '',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl',
    '2xl' => 'modal-xl',
][$maxWidth];
@endphp

<div 
    class="modal fade" 
    id="{{ $name }}"
    tabindex="-1" 
    aria-labelledby="{{ $name }}-label"
    aria-hidden="true"
    @if($show) data-bs-backdrop="static" @endif
>
    <div class="modal-dialog {{ $maxWidth }} modal-dialog-centered">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>

@if($show)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = new bootstrap.Modal(document.getElementById('{{ $name }}'), {
                keyboard: false
            });
            modal.show();
        });
    </script>
@endif