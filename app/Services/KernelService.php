<?php

namespace App\Services;

use App\Models\ScheduledJobCommand;
use Cron\CronExpression;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Symfony\Component\Console\Output\BufferedOutput;

class KernelService
{
    public function executeDueCommands(): void
    {
        $now = Carbon::now()->startOfMinute();

        $commands = ScheduledJobCommand::where('active', '1')->get();

        foreach ($commands as $command) {
            try {
                $cron = new CronExpression($command->scheduled_for);

                if ($cron->isDue($now)) {
                    $output = new BufferedOutput();

                    $parameters = [];
                    foreach ($command->parameters as $parameter) {
                        $parts = explode('=', $parameter, 2);
                        if (count($parts) === 2) {
                            $parameters[$parts[0]] = $parts[1]; // ['--user' => 'rodmayes@gmail.com']
                        }
                    }

                    Artisan::call($command->command, $parameters, $output);

                    $outputBuffer = $output->fetch();

                    Log::info('ScheduledJobCommand executed: ' . $command->command);
                    Log::debug($outputBuffer);
                }

            } catch (\Throwable $e) {
                Log::error('Error executing ScheduledJobCommand ' . $command->command, [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }
}
