<?php

namespace BaWe\ProsCons\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Webkul\Product\Repositories\ProductRepository;
use BaWe\ProsCons\Repositories\ProsConRepository;
use BaWe\ProsCons\Http\Requests\ProsConsRequest;

class ProsConsController extends Controller
{
    public function __construct(
        protected ProductRepository $productRepository,
        protected ProsConRepository $prosConRepository,
    ) {}

    public function edit(int $productId)
    {
        $product = $this->productRepository->findOrFail($productId);
        $items   = $this->prosConRepository->getByProduct($productId);

        return view('proscons::admin.pros_cons.edit', [
            'product' => $product,
            'pros'    => $items['pros'],
            'cons'    => $items['cons'],
        ]);
    }

    public function update(ProsConsRequest $request, int $productId)
    {
        $this->productRepository->findOrFail($productId);

        DB::transaction(function () use ($request, $productId) {
            $pros = $request->input('pros', []);
            $cons = $request->input('cons', []);
            $this->prosConRepository->upsertMany($productId, $pros, $cons);
        });

        session()->flash('success', 'Pros & Cons opgeslagen.');

        return Redirect::route('admin.proscons.edit', $productId);
    }
}
