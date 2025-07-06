<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function __construct()
    {}

    public function sendTelegramConfirmation($botToken, $chatId, User $user)
    {
        $link = route('telegram.login.confirm', $user->login_token);
        $text = "🔐 *Nuevo intento de login*\n\nUsuario: `{$user->email}`\n\n👉 [Confirmar acceso]({$link})";

        // To user
        $this->sendMessage($text, $botToken, $chatId, $user);
        // To channel
        $this->sendMessage($text, $botToken, $chatId);
    }

    public function sendMessage($text, $botToken, $chatId = null, User $user = null){
        if($user){
            // Mensaje al usuario
            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $user->telegram_chat_id,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
            ]);
        }else{
            // Al canal
            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
            ]);
        }

    }
}

