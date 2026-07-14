@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md border-[#d8d0ea] shadow-sm focus:border-[#d91426] focus:ring-[#d91426]']) }}>
