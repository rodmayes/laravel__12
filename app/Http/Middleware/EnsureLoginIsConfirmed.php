<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLoginIsConfirmed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(env('TELEGRAM_AUTHENTICATION', false)) {
            if (auth()->check() && is_null(auth()->user()->login_confirmed_at)) {
                auth()->logout();
                return redirect()->route('login')->withErrors(['auth' => 'Confirma el login en Telegram.']);
            }
        }

        return $next($request);
    }
}
