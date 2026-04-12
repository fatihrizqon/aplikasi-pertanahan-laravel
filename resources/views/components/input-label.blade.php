@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm text-gray-700 dark:text-neutral-400']) }}>
    {{ $value ?? $slot }}
</label>
