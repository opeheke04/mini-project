<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesReportController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'period' => ['nullable', 'in:day,month'],
            'cashier_id' => ['nullable', 'integer', 'exists:users,id'],
            'payment_method' => ['nullable', 'in:Tunai,QRIS,Debit,Kredit,E-Wallet,Transfer'],
        ]);

        $from = Carbon::parse($validated['from'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($validated['to'] ?? ($validated['from'] ?? now()->endOfMonth()->toDateString()))->endOfDay();
        $period = $validated['period'] ?? 'day';
        $search = trim($validated['search'] ?? '');
        $cashierId = $validated['cashier_id'] ?? null;
        $paymentMethod = $validated['payment_method'] ?? null;

        $salesForRange = Sale::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($cashierId, fn (Builder $query) => $query->where('user_id', $cashierId))
            ->when($paymentMethod, fn (Builder $query) => $query->where('payment_method', $paymentMethod));

        $summary = (clone $salesForRange)
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(total), 0) as revenue')
            ->first();

        $driver = DB::connection()->getDriverName();
        $periodExpression = match ([$driver, $period]) {
            ['sqlite', 'day'], ['mysql', 'day'] => 'DATE(created_at)',
            ['sqlite', 'month'] => "strftime('%Y-%m', created_at)",
            ['mysql', 'month'] => "DATE_FORMAT(created_at, '%Y-%m')",
            default => throw new \RuntimeException("Sales reports do not support the [{$driver}] database driver."),
        };

        $periodSummaries = (clone $salesForRange)
            ->selectRaw("{$periodExpression} as bucket, COUNT(*) as transaction_count, COALESCE(SUM(total), 0) as revenue")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $sales = Sale::query()
            ->with('user:id,name')
            ->whereBetween('created_at', [$from, $to])
            ->when($cashierId, fn (Builder $query) => $query->where('user_id', $cashierId))
            ->when($paymentMethod, fn (Builder $query) => $query->where('payment_method', $paymentMethod))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('transaction_number', 'like', "%{$search}%")
                        ->orWhere('customer_type', 'like', "%{$search}%")
                        ->orWhere('customer_contact', 'like', "%{$search}%")
                        ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('items', fn (Builder $itemQuery) => $itemQuery->where('product_name', 'like', "%{$search}%"));
                });
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'periodSummaries' => $periodSummaries,
            'transactionCount' => (int) $summary->transaction_count,
            'revenue' => (float) $summary->revenue,
            'from' => $from,
            'to' => $to,
            'period' => $period,
            'search' => $search,
            'cashierId' => $cashierId,
            'paymentMethod' => $paymentMethod,
            'cashiers' => User::query()->where('role', 'kasir')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(Sale $sale): View
    {
        $sale->load(['user:id,name', 'items']);

        return view('sales.show', compact('sale'));
    }
}
