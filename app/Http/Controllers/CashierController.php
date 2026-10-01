<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CashierController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'category', 'price', 'stock']);

        return view('cashier.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'customer_type' => ['nullable', 'string', 'max:100'],
            'customer_contact' => ['nullable', 'string', 'max:50'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'other_fee' => ['nullable', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:Tunai,QRIS,Debit,Kredit,E-Wallet,Transfer'],
        ]);

        $sale = DB::transaction(function () use ($request, $validated) {
            $productIds = collect($validated['items'])->pluck('product_id');
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotalCents = 0;
            $saleItems = [];

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['product_id']);
                if (!$product || !$product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => 'Salah satu produk tidak lagi tersedia.',
                    ]);
                }
                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak mencukupi.",
                    ]);
                }

                $unitPriceCents = $this->toCents($product->price);
                $lineTotalCents = $unitPriceCents * $item['quantity'];
                $subtotalCents += $lineTotalCents;
                $saleItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price_cents' => $unitPriceCents,
                    'line_total_cents' => $lineTotalCents,
                ];
            }

            $discountPercent = (float) ($validated['discount_percent'] ?? 0);
            $discountAmountCents = $this->toCents($validated['discount_amount'] ?? 0);
            $taxCents = $this->toCents($validated['tax'] ?? 0);
            $otherFeeCents = $this->toCents($validated['other_fee'] ?? 0);
            $totalCents = max(
                0,
                $subtotalCents
                    - (int) round($subtotalCents * $discountPercent / 100)
                    - $discountAmountCents
                    + $taxCents
                    + $otherFeeCents
            );
            $amountPaidCents = $this->toCents($validated['amount_paid']);

            if ($amountPaidCents < $totalCents) {
                throw ValidationException::withMessages([
                    'amount_paid' => 'Uang pembayaran masih kurang.',
                ]);
            }

            $sale = Sale::create([
                'transaction_number' => 'TRX-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(6)),
                'user_id' => $request->user()->id,
                'customer_type' => $validated['customer_type'] ?? null,
                'customer_contact' => $validated['customer_contact'] ?? null,
                'subtotal' => $this->fromCents($subtotalCents),
                'discount_percent' => $discountPercent,
                'discount_amount' => $this->fromCents($discountAmountCents),
                'tax' => $this->fromCents($taxCents),
                'other_fee' => $this->fromCents($otherFeeCents),
                'total' => $this->fromCents($totalCents),
                'amount_paid' => $this->fromCents($amountPaidCents),
                'change_due' => $this->fromCents($amountPaidCents - $totalCents),
                'payment_method' => $validated['payment_method'],
            ]);

            foreach ($saleItems as $item) {
                $product = $item['product'];
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $this->fromCents($item['unit_price_cents']),
                    'quantity' => $item['quantity'],
                    'line_total' => $this->fromCents($item['line_total_cents']),
                ]);
                $product->decrement('stock', $item['quantity']);
            }

            return $sale;
        });

        return response()->json([
            'message' => 'Transaksi berhasil disimpan.',
            'transaction' => [
                'number' => $sale->transaction_number,
                'total' => $sale->total,
                'change' => $sale->change_due,
            ],
        ], 201);
    }

    private function toCents(string|int|float $amount): int
    {
        return (int) round((float) $amount * 100);
    }

    private function fromCents(int $amount): string
    {
        return intdiv($amount, 100).'.'.str_pad((string) ($amount % 100), 2, '0', STR_PAD_LEFT);
    }
}
