<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CashierAccountController extends Controller
{
    public function index(): View
    {
        $cashiers = User::query()
            ->where('role', 'kasir')
            ->latest()
            ->paginate(15);

        return view('admin.cashier-accounts.index', compact('cashiers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::min(16)->letters()->numbers()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => 'kasir',
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.cashier-accounts.index')
            ->with('success', 'Akun kasir berhasil dibuat.');
    }
}
