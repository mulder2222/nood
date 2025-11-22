<!-- SEO Meta Content -->
@push('meta')
    <meta
        name="description"
        content="{{ trim($category->meta_description) != "" ? $category->meta_description : \Illuminate\Support\Str::limit(strip_tags($category->description), 120, '') }}"
    />

    <meta
        name="keywords"
        content="{{ $category->meta_keywords }}"
    />

    @if (core()->getConfigData('catalog.rich_snippets.categories.enable'))
        <script type="application/ld+json">
            {!! app('Webkul\Product\Helpers\SEO')->getCategoryJsonLd($category) !!}
        </script>
    @endif
@endPush

<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ trim($category->meta_title) != "" ? $category->meta_title : $category->name }}
    </x-slot>

    {!! view_render_event('bagisto.shop.categories.view.banner_path.before') !!}

    <!-- Hero Image -->
    @if ($category->banner_path)
        <div class="container mt-8 px-[60px] max-lg:px-8 max-md:mt-4 max-md:px-4">
            <x-shop::media.images.lazy
                class="aspect-[4/1] max-h-full max-w-full rounded-xl"
                src="{{ $category->banner_url }}"
                alt="{{ $category->name }}"
                width="1320"
                height="300"
            />
        </div>
    @endif

    {!! view_render_event('bagisto.shop.categories.view.banner_path.after') !!}

    {!! view_render_event('bagisto.shop.categories.view.description.before') !!}

    {{-- Description moved inside product column below for layout alignment --}}

    {!! view_render_event('bagisto.shop.categories.view.description.after') !!}

    @php
        // Pre-fetch direct active children for later inline rendering inside product column
        $childCategories = $category->children()->where('status', 1)->orderBy('position')->get();
    @endphp

    @if (in_array($category->display_mode, [null, 'products_only', 'products_and_description']))
        <!-- Category Vue Component -->
        <v-category>
            <!-- Category Shimmer Effect -->
            <x-shop::shimmer.categories.view />
        </v-category>
    @endif

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-category-template"
        >
            <div class="container px-[20px] max-lg:px-8 max-md:px-4">
                <div class="flex items-start gap-10 max-lg:gap-5 md:mt-10">
                    <!-- Product Listing Filters -->
                    @include('shop::categories.filters')

                    <!-- Product Listing Container -->
                    <div class="flex-1">
                        <h1 class="mb-5 text-3xl font-semibold leading-tight text-black max-md:mb-4 max-md:text-2xl max-sm:text-xl max-md:mt-4">
                            {{ trim($category->name) }}
                        </h1>
                        @if (in_array($category->display_mode, [null, 'description_only', 'products_and_description']))
                            @if ($category->description)
                                <div class="mb-6 max-md:mb-5 max-md:text-sm max-sm:text-xs">
                                    <div id="category-description" class="relative">
                                        <div id="category-description-content" class="overflow-hidden transition-all duration-300 break-words hyphens-auto" style="max-height:none; word-wrap: break-word; overflow-wrap: break-word; hyphens: auto;">
                                            {!! $category->description !!}
                                        </div>
                                        <div id="category-description-fade" class="pointer-events-none absolute inset-x-0 bottom-0 hidden h-10 bg-gradient-to-t from-white to-transparent"></div>
                                        <button
                                            type="button"
                                            id="category-description-toggle"
                                            class="mt-2 hidden text-sm font-medium text-navyBlue underline"
                                            aria-expanded="false"
                                            aria-controls="category-description-content"
                                        >
                                            Toon meer
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endif
                        @if(isset($childCategories) && $childCategories->count())
                            <div class="mb-8 rounded-xl bg-white/60 mt-4 shadow-sm max-md:mb-6">
                                <div class="grid grid-cols-4 gap-5 max-1280:grid-cols-3 max-md:grid-cols-2 max-sm:grid-cols-2">
                                    @foreach($childCategories as $child)
                                        @php $childProductCount = $child->products()->count(); @endphp
                                        <a href="{{ $child->url }}" class="group relative flex flex-col overflow-hidden rounded-lg border border-zinc-200 bg-white transition hover:border-navyBlue hover:shadow-sm">
                                            <div class="relative aspect-[4/3] w-full bg-[#eeeff0]">
                                                @if($child->banner_url)
                                                    <img src="{{ $child->banner_url }}" alt="{{ $child->name }}" class="h-full w-full object-cover" loading="lazy" />
                                                @elseif($child->logo_url)
                                                    <img src="{{ $child->logo_url }}" alt="{{ $child->name }}" class="h-full w-full object-contain" loading="lazy" />
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-xs text-zinc-400">Geen afbeelding</div>
                                                @endif
                                            </div>
                                            <div class="flex flex-1 flex-col p-4 max-sm:p-3">
                                                <span class="line-clamp-2 text-sm font-medium text-navyBlue group-hover:underline">{{ $child->name }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <!-- Desktop Product Listing Toolbar -->
                        <div class="max-md:hidden">
                            @include('shop::categories.toolbar')
                        </div>

                        <!-- Product List Card Container -->
                        <div
                            class="mt-8 grid grid-cols-1 gap-6"
                            v-if="(filters.toolbar.applied.mode ?? filters.toolbar.default.mode) === 'list'"
                        >
                            <!-- Product Card Shimmer Effect -->
                            <template v-if="isLoading">
                                <x-shop::shimmer.products.cards.list count="12" />
                            </template>

                            <!-- Product Card Listing -->
                            {!! view_render_event('bagisto.shop.categories.view.list.product_card.before') !!}

                            <template v-else>
                                <template v-if="products.length">
                                    <x-shop::products.card
                                        ::mode="'list'"
                                        v-for="product in products"
                                    />
                                </template>

                                <!-- Empty Products Container -->
                                <template v-else>
                                    <div class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center">
                                        <img
                                            class="max-md:h-[100px] max-md:w-[100px]"
                                            src="{{ bagisto_asset('images/thank-you.png') }}"
                                            alt="@lang('shop::app.categories.view.empty')"
                                        />

                                        <p
                                            class="text-xl max-md:text-sm"
                                            role="heading"
                                        >
                                            @lang('shop::app.categories.view.empty')
                                        </p>
                                    </div>
                                </template>
                            </template>

                            {!! view_render_event('bagisto.shop.categories.view.list.product_card.after') !!}
                        </div>

                        <!-- Product Grid Card Container -->
                        <div v-else class="mt-8 max-md:mt-5">
                            <h3 class="mt-4 mb-2">Alle producten in deze categorie</h3>
                            <!-- Product Card Shimmer Effect -->
                            <template v-if="isLoading">
                                <div class="grid grid-cols-4 gap-6 max-1280:grid-cols-3 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
                                    <x-shop::shimmer.products.cards.grid count="12" />
                                </div>
                            </template>

                            {!! view_render_event('bagisto.shop.categories.view.grid.product_card.before') !!}

                            <!-- Product Card Listing -->
                            <template v-else>
                                <template v-if="products.length">
                                    <div class="grid grid-cols-4 gap-6 max-1280:grid-cols-3 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
                                        <x-shop::products.card
                                            ::mode="'grid'"
                                            v-for="product in products"
                                        />
                                    </div>
                                </template>

                                <!-- Empty Products Container -->
                                <template v-else>
                                    <div class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center">
                                        <img
                                            class="max-md:h-[100px] max-md:w-[100px]"
                                            src="{{ bagisto_asset('images/thank-you.png') }}"
                                            alt="@lang('shop::app.categories.view.empty')"
                                        />

                                        <p
                                            class="text-xl max-md:text-sm"
                                            role="heading"
                                        >
                                            @lang('shop::app.categories.view.empty')
                                        </p>
                                    </div>
                                </template>
                            </template>

                            {!! view_render_event('bagisto.shop.categories.view.grid.product_card.after') !!}
                        </div>

                        {!! view_render_event('bagisto.shop.categories.view.load_more_button.before') !!}

                        <!-- Load More Button -->
                        <button
                            class="secondary-button mx-auto mt-14 block w-max rounded-2xl px-11 py-3 text-center text-base max-md:rounded-lg max-sm:mt-6 max-sm:px-6 max-sm:py-1.5 max-sm:text-sm"
                            @click="loadMoreProducts"
                            v-if="links.next && ! loader"
                        >
                            @lang('shop::app.categories.view.load-more')
                        </button>

                        <button
                            v-else-if="links.next"
                            class="secondary-button mx-auto mt-14 block w-max rounded-2xl px-[74.5px] py-3.5 text-center text-base max-md:rounded-lg max-md:py-3 max-sm:mt-6 max-sm:px-[50.8px] max-sm:py-1.5"
                        >
                            <!-- Spinner -->
                            <img
                                class="h-5 w-5 animate-spin text-navyBlue"
                                src="{{ bagisto_asset('images/spinner.svg') }}"
                                alt="Loading"
                            />
                        </button>

                        {!! view_render_event('bagisto.shop.categories.view.grid.load_more_button.after') !!}
                    </div>
                </div>
            </div>
        </script>
        <script>
            (function(){
                function init(){
                    if(document.documentElement.dataset.catDescInit) return;
                    const content=document.getElementById('category-description-content');
                    const toggle=document.getElementById('category-description-toggle');
                    const fade=document.getElementById('category-description-fade');
                    if(!content||!toggle||!fade) return;
                    document.documentElement.dataset.catDescInit='1';
                    const H=60;content.style.maxHeight='none';let full=content.scrollHeight;
                    if(full<=H+10){toggle.classList.add('hidden');fade.classList.add('hidden');return;}
                    let expanded=false;
                    const collapse=()=>{content.style.maxHeight=H+'px';fade.classList.remove('hidden');toggle.textContent='Toon meer';toggle.setAttribute('aria-expanded','false');};
                    const expand=()=>{full=content.scrollHeight;content.style.maxHeight=full+'px';fade.classList.add('hidden');toggle.textContent='Toon minder';toggle.setAttribute('aria-expanded','true');};
                    collapse();toggle.classList.remove('hidden');
                    const ro=new ResizeObserver(()=>{if(!expanded) collapse();});ro.observe(content);
                    toggle.addEventListener('click',()=>{expanded=!expanded;expanded?expand():collapse();});
                }
                if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',init,{once:true});} else {init();}
            })();
        </script>

        <script type="module">
            app.component('v-category', {
                template: '#v-category-template',

                data() {
                    return {
                        isMobile: window.innerWidth <= 767,

                        isLoading: true,

                        isDrawerActive: {
                            toolbar: false,

                            filter: false,
                        },

                        filters: {
                            toolbar: {
                                default: {},

                                applied: {},
                            },

                            filter: {},
                        },

                        products: [],

                        links: {},

                        loader: false,
                    }
                },

                computed: {
                    queryParams() {
                        let queryParams = Object.assign({}, this.filters.filter, this.filters.toolbar.applied);

                        return this.removeJsonEmptyValues(queryParams);
                    },

                    queryString() {
                        return this.jsonToQueryString(this.queryParams);
                    },
                },

                watch: {
                    queryParams() {
                        this.getProducts();
                    },

                    queryString() {
                        window.history.pushState({}, '', '?' + this.queryString);
                    },
                },

                methods: {
                    setFilters(type, filters) {
                        this.filters[type] = filters;
                    },

                    clearFilters(type, filters) {
                        this.filters[type] = {};
                    },

                    getProducts() {
                        this.isDrawerActive = {
                            toolbar: false,

                            filter: false,
                        };

                        document.body.style.overflow ='scroll';

                        this.$axios.get("{{ route('shop.api.products.index', ['category_id' => $category->id]) }}", {
                            params: this.queryParams
                        })
                            .then(response => {
                                this.isLoading = false;

                                this.products = response.data.data;

                                this.links = response.data.links;
                            }).catch(error => {
                                console.log(error);
                            });
                    },

                    loadMoreProducts() {
                        if (! this.links.next) {
                            return;
                        }

                        this.loader = true;

                        this.$axios.get(this.links.next)
                            .then(response => {
                                this.loader = false;

                                this.products = [...this.products, ...response.data.data];

                                this.links = response.data.links;
                            }).catch(error => {
                                console.log(error);
                            });
                    },

                    removeJsonEmptyValues(params) {
                        Object.keys(params).forEach(function (key) {
                            if ((! params[key] && params[key] !== undefined)) {
                                delete params[key];
                            }

                            if (Array.isArray(params[key])) {
                                params[key] = params[key].join(',');
                            }
                        });

                        return params;
                    },

                    jsonToQueryString(params) {
                        let parameters = new URLSearchParams();

                        for (const key in params) {
                            parameters.append(key, params[key]);
                        }

                        return parameters.toString();
                    }
                },
            });
        </script>
    @endPushOnce
</x-shop::layouts>
