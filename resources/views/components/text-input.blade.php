@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-amber-500 dark:focus:border-amber-400 focus:ring-amber-500 dark:focus:ring-amber-400 rounded-md shadow-sm']) }}>
