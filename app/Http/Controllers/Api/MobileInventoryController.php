<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockRestock;
use App\Services\StockRestockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileInventoryController extends Controller
{
    public function products(): JsonResponse
    {
        $products = Product::query()
            ->orderBy('name')
            ->get(['id', 'name', 'category', 'stock', 'is_active']);

        return response()->json([
            'data' => $products,
            'low_stock_threshold' => config('inventory.low_stock_threshold'),
        ]);
    }

    public function restocks(): JsonResponse
    {
        $restocks = StockRestock::query()
            ->with('user:id,name')
            ->latest('received_at')
            ->latest('id')
            ->paginate(25);

        return response()->json($restocks);
    }

    public function storeRestock(Request $request, StockRestockService $restockService): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'received_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $restock = $restockService->record(
            (int) $validated['product_id'],
            (int) $request->user()->id,
            (int) $validated['quantity'],
            $validated['supplier'] ?? null,
            $validated['received_at'] ?? null,
            $validated['notes'] ?? null,
        );

        return response()->json([
            'message' => 'Stok berhasil ditambahkan.',
            'data' => $restock->load('user:id,name'),
        ], 201);
    }
}
