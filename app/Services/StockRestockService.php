<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockRestock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockRestockService
{
    private const MAX_STOCK = 2147483647;

    public function record(
        int $productId,
        int $userId,
        int $quantity,
        ?string $supplier,
        ?string $receivedAt,
        ?string $notes,
    ): StockRestock {
        return DB::transaction(function () use ($productId, $userId, $quantity, $supplier, $receivedAt, $notes): StockRestock {
            $product = Product::query()
                ->whereKey($productId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($quantity > self::MAX_STOCK - $product->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah restock melebihi kapasitas stok maksimum.',
                ]);
            }

            $restock = StockRestock::query()->create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'supplier' => $supplier,
                'received_at' => $receivedAt ?? now(),
                'notes' => $notes,
            ]);

            $product->increment('stock', $quantity);

            return $restock;
        });
    }
}
