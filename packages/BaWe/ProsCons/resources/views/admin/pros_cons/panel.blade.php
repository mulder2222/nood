@inject('prosConRepo', 'BaWe\\ProsCons\\Repositories\\ProsConRepository')
@php
    $items = $prosConRepo->getByProduct($product->id);
    $pros  = collect($items['pros'] ?? []);
    $cons  = collect($items['cons'] ?? []);
@endphp

<div
    id="proscons-panel-{{ $product->id }}"
    data-product-id="{{ $product->id }}"
    data-save-url="{{ route('admin.proscons.update', $product->id) }}"
    class="box-shadow rounded bg-white p-4 dark:bg-gray-900"
>
    <div class="mb-4 flex items-center justify-between">
        <p class="text-base font-semibold text-gray-800 dark:text-white">
            Plus- en minpunten
        </p>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.proscons.edit', $product->id) }}" class="transparent-button">Volledig bewerken</a>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <p class="mb-2 font-medium text-gray-700 dark:text-white">Pluspunten</p>
            <div data-list="pros" class="grid gap-2">
                @forelse ($pros as $pro)
                    <div class="flex items-start gap-2">
                        <textarea name="pros[][text]" class="pros-input w-full rounded border border-gray-300 p-2 text-sm dark:border-gray-800 dark:bg-gray-950 dark:text-white" rows="2">{{ $pro->text }}</textarea>
                        <button type="button" class="icon-trash text-xl text-red-600" title="Verwijderen" data-action="remove"></button>
                    </div>
                @empty
                @endforelse
            </div>
            <button type="button" class="mt-2 small-secondary-button" onclick="window.BaWeProsCons && BaWeProsCons.add('pros','proscons-panel-{{ $product->id }}')">+ Pluspunt</button>
        </div>

        <div>
            <p class="mb-2 font-medium text-gray-700 dark:text-white">Minpunten</p>
            <div data-list="cons" class="grid gap-2">
                @forelse ($cons as $con)
                    <div class="flex items-start gap-2">
                        <textarea name="cons[][text]" class="cons-input w-full rounded border border-gray-300 p-2 text-sm dark:border-gray-800 dark:bg-gray-950 dark:text-white" rows="2">{{ $con->text }}</textarea>
                        <button type="button" class="icon-trash text-xl text-red-600" title="Verwijderen" data-action="remove"></button>
                    </div>
                @empty
                @endforelse
            </div>
            <button type="button" class="mt-2 small-secondary-button" onclick="window.BaWeProsCons && BaWeProsCons.add('cons','proscons-panel-{{ $product->id }}')">+ Minpunt</button>
        </div>
    </div>

    <script>
        (function() {
            if (!window.BaWeProsCons) {
                window.BaWeProsCons = {
                    add(type, rootId) {
                        const root = document.getElementById(rootId);
                        if (!root) return;
                        const listEl = root.querySelector(`[data-list="${type}"]`);
                        const cls = type === 'pros' ? 'pros-input' : 'cons-input';
                        const placeholder = type === 'pros' ? 'Voeg een pluspunt toe' : 'Voeg een minpunt toe';
                        const nameAttr = type === 'pros' ? 'pros[][text]' : 'cons[][text]';

                        const row = document.createElement('div');
                        row.className = 'flex items-start gap-2';
                        row.innerHTML = `
                            <textarea name="${nameAttr}" class="${cls} w-full rounded border border-gray-300 p-2 text-sm dark:border-gray-800 dark:bg-gray-950 dark:text-white" rows="2" placeholder="${placeholder}"></textarea>
                            <button type="button" class="icon-trash text-xl text-red-600" title="Verwijderen" onclick="window.BaWeProsCons && BaWeProsCons.remove(this)"></button>
                        `;
                        listEl.appendChild(row);
                    },
                    remove(btn) {
                        const row = btn.closest('div');
                        row && row.remove();
                    },
                    init(rootId) {
                        const root = document.getElementById(rootId);
                        if (!root) return;
                        const prosHas = root.querySelectorAll('.pros-input').length > 0;
                        const consHas = root.querySelectorAll('.cons-input').length > 0;
                        if (!prosHas) this.add('pros', rootId);
                        if (!consHas) this.add('cons', rootId);
                    }
                };
            }

            // Init current panel
            window.BaWeProsCons.init('proscons-panel-{{ $product->id }}');
        })();
    </script>
</div>
