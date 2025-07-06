<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if(env('TELEGRAM_AUTHENTICATION', false)) {
            $user = auth()->user();
            $user->update([
                'login_token' => Str::uuid(),
                'login_confirmed_at' => null, // 👈 Reinicia confirmación SIEMPRE
            ]);

            $botToken = config('services.telegram.bot_token');
            $chatId = config('services.telegram.chat_id');

            // Enviar notificación a Telegram
            (new TelegramService)->sendTelegramConfirmation($botToken, $chatId, $user);

            auth()->logout(); // 👈 Expulsa hasta que confirme
            return redirect()->route('login')->with('message', 'Confirma el login en Telegram.');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
