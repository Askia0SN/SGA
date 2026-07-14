@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-bold text-[#27185f]']) }}>
    {{ $value ?? $slot }}
</label>
