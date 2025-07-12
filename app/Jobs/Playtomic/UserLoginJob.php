<?php

namespace App\Jobs\Playtomic;

use App\Models\User;
use App\Services\Playtomic\PlaytomicHttpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class UserLoginJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $userId;
    public int $bookingId;

    public function __construct(int $userId, $bookingId = null)
    {
        $this->userId = $userId;
        $this->bookingId = $bookingId;
    }

    public function handle(){
        $user = User::byEmail($this->userId)->orWhere('id', $this->userId)->first();
        if(!$user){
            Log::error('No user found');
        }

        try {
            $login_response = (new PlaytomicHttpService($user->id))->login();
            if (!$login_response) {
                Log::debug('NOT Logged');
            }
            Log::info('Logged '.$user->email);

        }catch (\Exception $e){
            Log::error('Error Login: '.$e->getMessage());

        }
    }
}
