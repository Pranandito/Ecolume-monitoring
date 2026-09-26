<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 text-neutral-200 bg-neutral-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-neutral-700']) }}>
    {{ $slot }}
</button>