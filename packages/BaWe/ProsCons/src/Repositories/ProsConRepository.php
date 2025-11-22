<?php

namespace BaWe\ProsCons\Repositories;

use BaWe\ProsCons\Models\ProsCon;

class ProsConRepository
{
    public function getByProduct(int $productId): array
    {
        $items = ProsCon::where('product_id', $productId)
            ->orderBy('type')
            ->orderBy('position')
            ->get();

        return [
            'pros' => $items->where('type', 'pro')->values()->all(),
            'cons' => $items->where('type', 'con')->values()->all(),
        ];
    }

    public function upsertMany(int $productId, array $pros, array $cons): void
    {
        ProsCon::where('product_id', $productId)->delete();

        $pos = 0;
        foreach ($pros as $row) {
            $text = trim($row['text'] ?? '');
            if ($text === '') continue;

            ProsCon::create([
                'product_id' => $productId,
                'type'       => 'pro',
                'text'       => $text,
                'position'   => $pos++,
            ]);
        }

        $pos = 0;
        foreach ($cons as $row) {
            $text = trim($row['text'] ?? '');
            if ($text === '') continue;

            ProsCon::create([
                'product_id' => $productId,
                'type'       => 'con',
                'text'       => $text,
                'position'   => $pos++,
            ]);
        }
    }
}
