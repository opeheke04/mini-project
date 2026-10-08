<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\ReceiptPrinter;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class StrukController extends Controller
{
    public function __construct(protected ReceiptPrinter $printer)
    {
    }

    public function struk(?Sale $sale = null): JsonResponse
    {
        if ($sale) {
            $this->printer->printSale($sale);

            return response()->json([
                'status' => 'success',
                'message' => 'Struk berhasil dicetak.',
                'transaction' => [
                    'id' => $sale->id,
                    'number' => $sale->transaction_number,
                ],
            ]);
        }

        return response()->json([
            'status' => 'demo',
            'message' => 'Endpoint demo struk aktif. Gunakan route transaksi untuk mencetak struk nyata.',
        ]);
    }

    public function print(Sale $sale): JsonResponse
    {
        $sale->loadMissing(['user:id,name', 'items']);
        $printed = $this->printer->printSale($sale);

        return response()->json([
            'status' => $printed ? 'success' : 'skipped',
            'message' => $printed
                ? 'Struk berhasil dicetak.'
                : 'Printer tidak aktif atau gagal diakses. Transaksi tetap tersimpan.',
            'transaction' => [
                'id' => $sale->id,
                'number' => $sale->transaction_number,
                'total' => $sale->total,
                'change' => $sale->change_due,
            ],
        ]);
    }

    public function preview(Sale $sale): View
    {
        $sale->loadMissing(['user:id,name', 'items']);

        return view('receipts.thermal', compact('sale'));
    }
}
