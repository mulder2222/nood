{!! view_render_event('bagisto.shop.layout.footer.before') !!}

<!--
    The category repository is injected directly here because there is no way
    to retrieve it from the view composer, as this is an anonymous component.
-->
@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')
@inject('categoryRepository', 'Webkul\Category\Repositories\CategoryRepository')

<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $channel = core()->getCurrentChannel();
    // Retrieve the visible category tree. Depending on Bagisto version this can return:
    // 1) A root category object that has a ->children collection
    // 2) A Collection of categories (already the children of root)
    // We normalize to a collection of main (level-1) categories.
    $rawTree = $categoryRepository->getVisibleCategoryTree($channel->root_category_id);

    if ($rawTree instanceof \Illuminate\Support\Collection) {
        $mainCategories = $rawTree;
    } else {
        $mainCategories = collect($rawTree?->children ?? []);
    }

    // Only keep active categories (level-1) initially.
    $mainCategories = $mainCategories->filter(fn ($c) => $c->status);

    $customization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);
@endphp

<footer class="mt-9 bg-white text-black max-sm:mt-10">
    <div class="lg:container lg:px-0 mx-auto px-[60px] pt-14 max-sm:px-4">
        <!-- Newsletter (desktop full width top; mobile kept order 1) -->
        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.before') !!}
        @if (core()->getConfigData('customer.settings.newsletter.subscription'))
            <div class="grid gap-8 max-1060:order-1 items-start lg:flex lg:gap-12 lg:items-start">
                <div class="grid gap-3 min-w-[320px] w-full lg:w-3/5 pr-0 lg:pr-4">
                    <h3 class="text-[30px] font-semibold leading-9 max-xl:text-[28px] max-sm:text-lg">
                        Meld je aan en mis geen enkele deal!
                    </h3>
                    <p class="max-w-[760px] text-[15px] leading-6 opacity-90 max-sm:text-xs">
                        Ontvang exclusieve aanbiedingen, het laatste SentraShop nieuws en €10 korting op je eerste bestelling. Schrijf je vandaag nog in en blijf op de hoogte van de beste deals!
                    </p>
                </div>
                <div class="mt-1 lg:mt-0 w-full lg:w-2/5 max-w-none">
                    <x-shop::form :action="route('shop.subscription.store')" class="max-sm:mt-0">
                        <div class="flex w-full flex-col gap-3 max-sm:gap-2">
                <div class="flex flex-col gap-3 lg:grid lg:grid-cols-[1fr_auto] lg:gap-2 lg:items-stretch">
                                <x-shop::form.control-group.control
                                    type="email"
            class="block w-full rounded-none bg-white/90 p-4 text-base text-black placeholder:text-gray-500 focus:border-transparent focus:ring-0 max-sm:text-sm lg:rounded-l lg:rounded-r-none"
                                    name="email"
                                    rules="required|email"
                                    label="Email"
                                    :aria-label="trans('smarthome::app.components.layouts.footer.email')"
                                    :placeholder="trans('smarthome::app.components.layouts.footer.example-email')"
                                />
                                <button
                                    type="submit"
            class="inline-flex shrink-0 items-center justify-center bg-sentraRed px-8 py-4 font-medium text-white transition-colors hover:bg-sentraRedHover focus:outline-none focus:ring-2 focus:ring-white/40 max-md:text-xs lg:rounded-r lg:rounded-l-none max-sm:w-full"
                                >
                                    @lang('smarthome::app.components.layouts.footer.subscribe')
                                </button>
                            </div>
                            <x-shop::form.control-group.error control-name="email" />
                            <p class="text-xs opacity-80 leading-5">
                                Door je in te schrijven ga je akkoord met de <a href="#" class="underline underline-offset-2">algemene voorwaarden</a>
                            </p>
                        </div>
                    </x-shop::form>
                </div>
            </div>
        @endif
        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.after') !!}
    </div>

    <!-- Separator -->
    <div class="lg:container mx-auto mt-14 h-px bg-gray-200 max-1180:mt-12 max-md:mt-10 max-sm:hidden"></div>

    <!-- Desktop link columns with logo (hidden on mobile) -->
    <div class="lg:container lg:px-0 mx-auto px-[60px] mt-12 max-sm:hidden">
        @php
            // Prepare desktop category columns (limit to 3) each with active children
            $desktopCategories = [];
            if ($mainCategories && $mainCategories->count()) {
                foreach ($mainCategories as $cat) {
                    $children = collect($cat->children ?? [])->filter(fn($c) => $c->status);
                    if ($children->count()) {
                        $desktopCategories[] = [
                            'title'    => $cat->name,
                            'children' => $children,
                        ];
                    }
                    if (count($desktopCategories) === 3) {
                        break; // limit to 3 categories for desktop
                    }
                }
            }

            // Collect all footer links into a single flat info array for desktop
            $infoLinks = [];
            if ($customization?->options) {
                foreach ($customization->options as $section) {
                    usort($section, fn($a,$b) => $a['sort_order'] <=> $b['sort_order']);
                    foreach ($section as $lnk) {
                        $infoLinks[] = $lnk;
                    }
                }
            }
        @endphp
        <div class="grid gap-20 max-xl:gap-16 max-lg:gap-12 max-md:gap-8 items-start" style="grid-template-columns:240px repeat(auto-fit,minmax(140px,1fr));">
            <!-- Brand column -->
            <div class="pt-3">
                <a href="{{ route('shop.home.index') }}" aria-label="{{ $channel->name }}" class="group inline-flex items-center">
                    <span class="flex items-center justify-center rounded-md bg-white/90 p-3">
                        <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}" alt="{{ $channel->name }}" class="h-full w-auto object-contain" loading="lazy" decoding="async" />
                    </span>
                </a>
            </div>
            <!-- Category columns -->
            @foreach ($desktopCategories as $categoryBlock)
                <div class="text-sm">
                    <h4 class="mb-5 text-[17px] font-semibold tracking-wide">{{ $categoryBlock['title'] }}</h4>
                    <ul class="grid gap-3">
                        @foreach ($categoryBlock['children'] as $child)
                            <li>
                                <a href="{{ $child->url }}" class="transition-opacity hover:opacity-80">{{ $child->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            @if (count($infoLinks))
                <div class="text-sm">
                    <h4 class="mb-5 text-[17px] font-semibold tracking-wide">Informatie</h4>
                    <ul class="grid gap-3">
                        @foreach ($infoLinks as $link)
                            @php $isViewAll = Str::startsWith($link['title'], 'Bekijk alle'); @endphp
                            <li @class(['mt-4' => $isViewAll])>
                                <a href="{{ $link['url'] }}" @class(['transition-opacity hover:opacity-80','font-medium'=> $isViewAll])>{{ $link['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <!-- Mobile retains previous structure (accordion etc.) -->
    <div class="lg:container mx-auto hidden max-1060:block px-[60px] max-1180:px-10 max-md:px-8 max-sm:px-4 max-sm:pt-8">
        <!-- Mobile logo block (order 2 previously) -->
        <div class="mb-4 flex flex-col gap-4 max-1060:order-2">
            <a href="{{ route('shop.home.index') }}" class="inline-block" aria-label="{{ $channel->name }}">
                <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}" alt="{{ $channel->name }}" class="h-12 w-auto max-sm:h-10" loading="lazy" decoding="async" />
            </a>
        </div>

        <!-- Reuse original mobile accordions -->
        <div class="w-full">
            @if ($mainCategories && $mainCategories->count())
                @foreach ($mainCategories as $cat)
                    @php $children = collect($cat->children ?? [])->filter(fn($c) => $c->status); @endphp
                    @if ($children->count())
                        <x-shop::accordion :is-active="false" class="mb-2 !w-full rounded-xl bg-white shadow-sm max-sm:rounded-lg">
                            <x-slot:header class="rounded-t-lg bg-white font-medium max-md:p-2.5 max-sm:px-3 max-sm:py-2 max-sm:text-sm">{{ $cat->name }}</x-slot:header>
                            <x-slot:content class="!bg-transparent !p-4">
                                <ul class="grid gap-3 text-sm">
                                    @foreach ($children as $child)
                                        <li><a href="{{ $child->url }}" class="text-sm font-medium max-sm:text-xs">{{ $child->name }}</a></li>
                                    @endforeach
                                </ul>
                            </x-slot:content>
                        </x-shop::accordion>
                    @endif
                @endforeach
            @endif

            @if ($customization?->options)
                @php
                    $flatFooterLinks = [];
                    foreach ($customization->options as $section) {
                        usort($section, function ($a, $b) { return $a['sort_order'] - $b['sort_order']; });
                        foreach ($section as $link) { $flatFooterLinks[] = $link; }
                    }
                @endphp
                @if (count($flatFooterLinks))
                    <x-shop::accordion :is-active="false" class="mb-2 !w-full rounded-xl bg-white shadow-sm max-sm:rounded-lg">
                        <x-slot:header class="rounded-t-lg bg-white font-medium max-md:p-2.5 max-sm:px-3 max-sm:py-2 max-sm:text-sm">@lang('smarthome::app.components.layouts.footer.footer-content', ['default' => 'Menu'])</x-slot:header>
                        <x-slot:content class="!bg-transparent !p-4">
                            <ul class="grid gap-3 text-sm">
                                @foreach ($flatFooterLinks as $link)
                                    <li><a href="{{ $link['url'] }}" class="text-sm font-medium max-sm:text-xs">{{ $link['title'] }}</a></li>
                                @endforeach
                            </ul>
                        </x-slot:content>
                    </x-shop::accordion>
                @endif
            @endif
        </div>
    </div>

    <div class="lg:container mx-auto mt-10 border-t border-gray-200 px-[60px] py-6 text-sm max-1180:px-10 max-md:px-8 max-sm:px-4">
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}
        <div class="flex items-center justify-between max-sm:flex-col max-sm:gap-4">
            <p class="opacity-80">© {{ date('Y') }} {{ $channel->name }}. Alle rechten voorbehouden. Alle genoemde prijzen zijn inclusief BTW.</p>
        </div>
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
