<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class TelegramLoginController extends Controller
{
    public function webhook(Request $request){
        $data = $request->all();
        Log::info('Mensaje recibido de Telegram:', $data);

        // Puedes actuar en función del mensaje recibido
        // $data['message']['text'], $data['message']['chat']['id'], etc.

        return response()->noContent();
    }
    public function confirm(string $token)
    {
        $user = User::where('login_token', $token)->first();

        if (!$user) {
            abort(403, 'Token inválido o expirado');
        }

        // Limpia el token para evitar reuso
        $user->login_token = null;
        $user->save();

        Auth::login($user);

        return redirect()->route('home'); // o cualquier ruta post-login
    }
}
