@props([
    'href' => null,
    'text' => 'Kembali',
    'icon' => true,
    'variant' => 'default' // default, subtle, primary
])

@php
    $classes = match($variant) {
        'default' => 'inline-flex items-center gap-2 bg-teal-100 hover:bg-teal-200 text-teal-700 font-medium py-2 px-4 rounded-md text-sm transition shadow-sm',
        'subtle' => 'inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 font-medium py-2 px-3 rounded-md text-sm transition',
        'primary' => 'inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-medium py-2 px-4 rounded-md text-sm transition shadow-sm',
    };
@endphp

<a href="{{ $href ?? url()->previous() }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    @endif
    {{ $text }}
</a>
