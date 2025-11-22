<div class="lg:container p-2 lg:px-0 lg:p-4 mx-auto flex flex-col lg:flex-row space-y-0 lg:space-y-0 lg:space-x-4">
    <!-- Linker blok -->
    <div class="w-full lg:w-1/4 bg-white border border-gray-300 border-b-0 order-2 lg:order-1 mt-2 md:mt-0">
        <ul class="space-y-1">
            <li class="flex items-center p-3 gap-2 border-b border-gray-300">
                <img class="lazy w-16 h-16 object-cover" data-src="{{ bagisto_asset('images/intercomsystemen.jpeg') }}"> <a href="/intercomsystemen" class="text-black hover:underline">Intercomsystemen</a>
            </li>
            <li class="flex items-center p-3 gap-2 border-b border-gray-300">
                <img class="lazy w-16 h-16 object-cover" data-src="{{ bagisto_asset('images/Netwerksystemen.jpeg') }}"> <a href="/netwerksystemen" class="text-black hover:underline">Netwerksystemen</a>
            </li>
            <li class="flex items-center p-3 gap-2 border-b border-gray-300">
                <img class="lazy w-16 h-16 object-cover" data-src="/storage/theme/13/8wLpnEm1GyCdbjr9b5bbE0vEyCpCyjkGfaZbGUQI.webp"> <a href="/camerasystemen" class="text-black hover:underline">Camerasystemen</a>
            </li>
            <li class="flex items-center p-2.5 gap-2 border-b border-gray-300">
                <img class="lazy w-16 h-16 object-cover" data-src="/storage/theme/13/Rwrf0VdpRp15mBvOgEa3wfk99IjUi7bwoUICMCbs.webp"> <a href="/gereedschap" class="text-black hover:underline">Gereedschappen</a>
            </li>
            <li class="flex items-center p-3 gap-2 border-b border-gray-300">
                <img class="lazy w-16 h-16 object-cover" data-src="{{ bagisto_asset('images/alarmsystemen.jpeg') }}"> <a href="/alarmsystemen" class="text-black hover:underline">Alarmsystemen</a>
            </li>
        </ul>
    </div>
    <!-- Midden blok (banner) -->
    <div class="w-full lg:w-1/2 bg-white order-1 lg:order-2 overflow-hidden">
        <img class="lazy w-full h-[250px] lg:h-[455px] object-cover object-center" data-src="{{ bagisto_asset('images/homepage_banner_top.jpeg') }}" alt="Homepage banner" />
    </div>
    <!-- Rechter blok -->
    <div class="hidden sm:block w-full lg:w-1/4 bg-gray-100 lg:p-2 lg:ml-4 order-3 lg:order-3 space-y-7">
        <div class="space-y-2">
            <x-shop::layouts.opening-status />
        </div>
        <div class="">
            <img class="lazy w-full max-w-[340px] h-auto object-cover" data-src="/storage/theme/13/qbVwWgWYlNg4c81JmlfBj4eJOop2oqtbIladdf08.webp" alt="Contact" />
        </div>
    </div>
</div>
<!-- Second part -->
<div class="container lg:px-0 mx-auto mt-6 px-4 hidden sm:block">
    <div class="grid grid-cols-1 grid-cols-2 gap-4">
        <!-- Banner 1 -->
        <div class="overflow-hidden">
            <img class="lazy w-full h-40 object-cover object-center" data-src="/storage/theme/13/05u2PbamuPc4VM9QQg19Jb5nfRaA7zhumKsHYdEF.webp" alt="Banner 1" />
        </div>
        <!-- Banner 2 -->
        <div class="overflow-hidden">
            <img class="lazy w-full h-40 object-cover object-center" data-src="/storage/theme/13/TQW3fQ7cDktOhoLypTQgMPf9YUSxUXtWapx5K2t6.webp" alt="Banner 2" />
        </div>
    </div>
</div>
