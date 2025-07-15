<?php

namespace App\Jobs;

use App\Mail\sendLotteryNumbersMailable;
use App\Services\LotteryService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class getLotteryNumbersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $tries = 5;
    // Establece un límite de tiempo de ejecución alto
    public $timeout = 0; // Sin límite de tiempo
    // Establece un límite de memoria alto (en megabytes)
    public $memory = 1024; // Puedes ajustar esto según tus necesidades

    public int $maxCombinations = 10;
    public array $excludedNumbers = [];

    public string $uuid;

    public function __construct(array $excludedNumbers = [], int $maxCombinations = 10, string $uuid)
    {
        $this->excludedNumbers = $excludedNumbers;
        $this->maxCombinations = $maxCombinations;
        $this->uuid = $uuid;
    }

    public function handle():void
    {
        try{
            $service = new LotteryService();
            $service->setExcludedNumbers($this->excludedNumbers ?? []);
            $combinations = $service->getNumbersCombinations();

            if (count($combinations) > 0) {
                $combinations = array_slice($combinations, 0, $this->maxCombinations);
                foreach ($combinations as $combination) {
                    sort($combination);
                    Log::info('Generation magik numbers. Selected numbers: ' . implode(', ', $combination));
                }
                Cache::put("lottery_result_{$this->uuid}" , $combinations);
                Mail::to(env('MAIL_FROM_ADDRESS','rodmayes@gmail.com'))->send(new sendLotteryNumbersMailable($combinations));
            } else {
                echo "No valid combination found. \n\r";
                Log::error('No valid combination found.');
            }
            Log::info('Generation magik numbers finished.', $combinations);
        }catch(\Exception $e){
            Log::error('Generation magik numbers error: '.$e->getMessage());
        }
    }

}
