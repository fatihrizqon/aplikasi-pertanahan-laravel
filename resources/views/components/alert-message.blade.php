@props(['session'])

@php
$types = ['success', 'danger', 'warning', 'info'];
$type = collect($types)->first(fn ($t) => session()->has($t));
$message = $type ? session($type) : null;

$alertTypes = [
    'success' => [
        'bg' => 'bg-teal-50 dark:bg-teal-800/10',
        'border' => 'border-teal-200 dark:border-teal-900',
        'text' => 'text-teal-800 dark:text-teal-500',
        'iconColor' => 'text-teal-500 dark:text-teal-600',
        'icon' => '<i data-lucide="circle-check" class="w-4 h-4"></i>',
    ],
    'danger' => [
        'bg' => 'bg-red-50 dark:bg-red-800/10',
        'border' => 'border-red-200 dark:border-red-900',
        'text' => 'text-red-800 dark:text-red-500',
        'iconColor' => 'text-red-500 dark:text-red-600',
        'icon' => '<i data-lucide="triangle-alert" class="w-4 h-4"></i>',
    ],
    'warning' => [
        'bg' => 'bg-yellow-50 dark:bg-yellow-800/10',
        'border' => 'border-yellow-200 dark:border-yellow-900',
        'text' => 'text-yellow-800 dark:text-yellow-500',
        'iconColor' => 'text-yellow-500 dark:text-yellow-600',
        'icon' => '<i data-lucide="octagon-alert" class="w-4 h-4"></i>',
    ],
    'info' => [
        'bg' => 'bg-blue-50 dark:bg-blue-800/10',
        'border' => 'border-blue-200 dark:border-blue-900',
        'text' => 'text-blue-800 dark:text-blue-500',
        'iconColor' => 'text-blue-500 dark:text-blue-600',
        'icon' => '<i data-lucide="badge-info" class="w-4 h-4"></i>',
    ],
];

$styles = $type ? $alertTypes[$type] : null;
@endphp

@if ($message && $styles)
<div id="dismiss-alert" class="hs-removing:translate-x-5 hs-removing:opacity-0 {{ $styles['bg'] }} {{ $styles['border'] }} {{ $styles['text'] }} rounded-lg p-4 text-sm" role="alert" tabindex="-1">
    <div class="flex items-start">
        <div class="shrink-0 mt-0.5 {{ $styles['iconColor'] }}">
            {!! $styles['icon'] !!}
        </div>
        <div class="ms-2 flex-1">
            <h3 class="font-medium">{{ $message }}</h3>
        </div>
        <button type="button" class="ms-auto -mx-1.5 -my-1.5 p-1.5 rounded-lg hover:bg-opacity-10 focus:outline-hidden {{ $styles['iconColor'] }}" data-hs-remove-element="#dismiss-alert">
            <span class="sr-only">Close</span>
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
</div>
@endif