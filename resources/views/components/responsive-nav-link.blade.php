@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-[#d91426] bg-[#fff0f2] py-2 pe-4 ps-3 text-start text-base font-bold text-[#27185f] transition duration-150 ease-in-out focus:outline-none'
            : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-semibold text-[#6d6684] transition duration-150 ease-in-out hover:border-[#d8d0ea] hover:bg-[#f7f5fb] hover:text-[#27185f] focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
