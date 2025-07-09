<?php

namespace App\Jobs;

use App\Http\Controllers\Lottery\LotteryController;
use App\Mail\sendLotteryNumbersMailable;
use App\Models\LotteryResults;
use App\Models\ScheduledJob;
use App\Services\LotteryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

    public $maxCombinations = 10;
    public string $uuid;

    public function __construct($maxCombinations = 10, ?string $uuid = null)
    {
        $this->maxCombinations = $maxCombinations;
        $this->uuid = $uuid ?? (string) Str::uuid();
    }
    public function handle():void
    {
        try{
            $scheduled = ScheduledJob::where('job_id', $this->uuid)->first();
            $scheduled->status = 'running';
            $scheduled->save();

            $service = new LotteryService();
            $service->setExcludedNumbers($request->excludedNumbers ?? []);
            $combinations =  $service->getNumbersCombinations();

            if (count($combinations) > 0) {
                $combinations = array_slice($combinations, 0, $this->maxCombinations);
                foreach ($combinations as $combination) {
                    sort($combination);
                    Log::info('Selected numbers: ' . implode(', ', $combination));
                    echo "Result \n\r" . implode(', ', $combination);
                }
                Mail::to(env('MAIL_FROM_ADDRESS','rodmayes@gmail.com'))->send(new sendLotteryNumbersMailable($combinations));
            } else {
                echo "No valid combination found. \n\r";
                Log::error('No valid combination found.');
            }
            $scheduled->status = 'success';
            echo "Finished \n\r";
        }catch(\Exception $e){
            $scheduled->status = 'success';
            Log::error($e->getMessage());
            echo "Error: ".$e->getMessage();
        }
        $scheduled->save();
    }

}
