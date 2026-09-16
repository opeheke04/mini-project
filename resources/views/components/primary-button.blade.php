<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary px-5 py-3 text-xs uppercase tracking-[0.16em] focus:ring-4']) }}>
    {{ $slot }}
</button>
