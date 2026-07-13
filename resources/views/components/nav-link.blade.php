@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center border-b-2 border-[#d91426] px-1 pt-1 text-sm font-bold leading-5 text-[#27185f] transition duration-150 ease-in-out focus:outline-none focus:border-[#d91426]'
            : 'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-semibold leading-5 text-[#6d6684] transition duration-150 ease-in-out hover:border-[#d8d0ea] hover:text-[#27185f] focus:outline-none focus:text-[#27185f]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
