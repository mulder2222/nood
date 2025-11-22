@if ($prices['final']['price'] < $prices['regular']['price'])


    <p class="font-bold text-red-500 max-sm:leading-4 text-xl flex items-center gap-2">
        {{ $prices['final']['formatted_price'] }}
    </p>
    <p class="max-sm:leading-4 text-gray-400 text-xs">
        Normaal
    </p>
    <p
        class="final-price font-medium text-zinc-500 line-through max-sm:leading-4 text-xs"
        aria-label="{{ $prices['regular']['formatted_price'] }}"
    >
        {{ $prices['regular']['formatted_price'] }}
    </p>
@else
    <p class="final-price font-semibold max-sm:leading-4">
        {{ $prices['regular']['formatted_price'] }}
    </p>
@endif

