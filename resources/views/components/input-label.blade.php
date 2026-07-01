@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[#979797]']) }}>
    {{ $value ?? $slot }}
</label>