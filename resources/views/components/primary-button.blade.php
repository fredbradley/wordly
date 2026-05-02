<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-emerald-600 border border-transparent rounded-xl font-semibold text-sm text-white tracking-wide hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-900 active:bg-emerald-700 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
