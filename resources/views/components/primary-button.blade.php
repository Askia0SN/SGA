<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-md border border-transparent bg-[#d91426] px-4 py-2 text-xs font-bold uppercase text-white transition duration-150 ease-in-out hover:bg-[#b70f1e] focus:bg-[#b70f1e] focus:outline-none focus:ring-2 focus:ring-[#d91426] focus:ring-offset-2 active:bg-[#8f0d19]']) }}>
    {{ $slot }}
</button>
