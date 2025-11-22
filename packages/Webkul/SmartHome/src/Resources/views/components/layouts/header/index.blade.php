{!! view_render_event('bagisto.shop.layout.header.before') !!}

@if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1 )
    <div class="max-lg:hidden">
        <x-shop::layouts.header.desktop.top />
    </div>
@endif

<header class="shadow-gray sticky top-0 z-10 bg-white shadow-sm max-lg:shadow-none">


    <x-shop::layouts.header.desktop />

    <!-- Navigation Menu -->
    <div class="bg-gray-100 border-t border-gray-200 max-lg:hidden text-black">
        <div class="flex container mx-auto p-4 space-x-4 pl-0">
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.before') !!}

            <v-desktop-category>
                <div class="flex items-center gap-5">
                    <span
                        class="shimmer h-6 w-20 rounded"
                        role="presentation"
                    ></span>
                    <span
                        class="shimmer h-6 w-20 rounded"
                        role="presentation"
                    ></span>
                    <span
                        class="shimmer h-6 w-20 rounded"
                        role="presentation"
                    ></span>
                </div>
            </v-desktop-category>

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.after') !!}
        </div>
    </div>

    <x-shop::layouts.header.mobile />
</header>

{!! view_render_event('bagisto.shop.layout.header.after') !!}
