@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-slate-800 border-slate-600 text-white placeholder-slate-400 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm w-full']) }}>
