<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        Log::info('Request selesai', [
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
        ]);
    }
}
