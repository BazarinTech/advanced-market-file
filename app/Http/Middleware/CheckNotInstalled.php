<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckNotInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (file_exists(storage_path('installed.lock'))) {
            return redirect()->route('login')->with('error', 'Application is already installed.');
        }

        return $next($request);
    }
}
