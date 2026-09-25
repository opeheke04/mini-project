<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
}
