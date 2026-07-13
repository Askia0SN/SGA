<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-md border border-[#d8d0ea] bg-white px-4 py-2 text-xs font-bold uppercase text-[#27185f] shadow-sm transition duration-150 ease-in-out hover:border-[#27185f] hover:bg-[#f7f5fb] focus:outline-none focus:ring-2 focus:ring-[#27185f] focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
