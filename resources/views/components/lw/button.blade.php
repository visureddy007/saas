<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-default']) }}>
    {{ $slot }}
</button>