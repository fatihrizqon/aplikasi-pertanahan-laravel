@props([
'name',
'options' => [],
'selected' => null,
'placeholder' => 'Pilih...',
'id' => null,
'disabled' => false,
'searchable' => false,
])

@php
$id = $id ?? $name;
$items = $options instanceof \Illuminate\Support\Collection ? $options->all() : $options;

$renderedOptions = '';
foreach ($items as $value => $label) {
$isSelected = ((string) $value === (string) $selected);
$renderedOptions .= '<option value="' . e($value) . '"' . ($isSelected ? ' selected' : '' ) . '>' . e($label) . '</option>' ; } $config=[ 'placeholder'=> $placeholder,
    'toggleTag' => '<button type="button" aria-expanded="false"></button>',
    'toggleClasses' => 'hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-2.5 sm:py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800',
    'dropdownClasses' => 'mt-2 z-50 w-full max-h-72 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto dark:bg-neutral-900 dark:border-neutral-700',
    'optionClasses' => 'py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800',
    'optionTemplate' => '<div class="flex justify-between items-center w-full"><span data-title></span><span class="hidden hs-selected:block"><svg class="shrink-0 size-3.5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <polyline points="20 6 9 18 4 13" />
            </svg></span></div>',
    'extraMarkup' => '<div class="absolute top-1/2 end-3 -translate-y-1/2"><svg class="shrink-0 size-3.5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path d="m6 9 6 6 6-6" />
        </svg></div>',
    ];

if ($searchable) {
    $config['hasSearch']         = true;
    $config['searchPlaceholder'] = 'Cari...';
    $config['searchWrapperClasses'] = 'bg-white dark:bg-neutral-900 p-2 sticky top-0 border-b border-gray-100 dark:border-neutral-700';
    $config['searchClasses']     = 'block w-full text-sm border border-red-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-300 dark:placeholder-neutral-500 py-2 px-3';
}

    $hsSelect = json_encode($config);
    @endphp

    <select id="{{ $id }}" name="{{ $name }}" {{ $disabled ? 'disabled' : '' }} data-hs-select="{{ $hsSelect }}">
        <option value="">{{ $placeholder }}</option>
        {!! $renderedOptions !!}
    </select>
