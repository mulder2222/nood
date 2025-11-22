<x-admin::layouts>
    <x-slot:title>
        {{ __('Pros & Cons — ') . $product->name }}
    </x-slot>

    <x-admin::form :action="route('admin.proscons.update', $product->id)" method="POST">
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <x-admin::form.control-group>
            <x-admin::form.control-group.label>
                {{ __('Pluspunten') }}
            </x-admin::form.control-group.label>
            <x-admin::form.control-group.control type="textarea" name="pros[0][text]" :value="$pros[0]->text ?? ''"/>
        </x-admin::form.control-group>

        <x-admin::form.control-group>
            <x-admin::form.control-group.label>
                {{ __('Minpunten') }}
            </x-admin::form.control-group.label>
            <x-admin::form.control-group.control type="textarea" name="cons[0][text]" :value="$cons[0]->text ?? ''"/>
        </x-admin::form.control-group>

        <x-slot name="actions">
            <button type="submit" class="btn btn-primary">
                {{ __('Opslaan') }}
            </button>
        </x-slot>
    </x-admin::form>
</x-admin::layouts>
