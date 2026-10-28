<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockRestock;
use App\Services\StockRestockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockRestockController extends Controller
{
    public function index(): View
    {
        return view('stock-restocks.index', [
            'products' => Product::query()->orderBy('name')->get(['id', 'name', 'stock', 'is_active']),
            'restocks' => StockRestock::query()
                ->with('user:id,name')
                ->latest('received_at')
                ->latest('id')
                ->paginate(20),
        ]);
    }

    public function store(Request $request, StockRestockService $restockService): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'received_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $restockService->record(
            (int) $validated['product_id'],
            (int) $request->user()->id,
            (int) $validated['quantity'],
            $validated['supplier'] ?? null,
            $validated['received_at'] ?? null,
            $validated['notes'] ?? null,
        );

        return redirect()
            ->route('stock-restocks.index')
            ->with('success', 'Stok berhasil ditambahkan dan riwayat restock tersimpan.');
    }
}
