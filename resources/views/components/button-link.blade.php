@props(['color' => 'red'])

<a
    {{ $attributes->merge(['type' => 'button', 'class' => "inline-flex items-center justify-center cursor-pointer px-4 py-2 bg-$color-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-$color-600 focus:outline-none focus:border-$color-700 focus:ring focus:ring-$color-200 active:bg-$color-600 disabled:opacity-25 transition"]) }}>
    {{ $slot }}
</a>