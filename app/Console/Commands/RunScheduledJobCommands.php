<?php

namespace App\Console\Commands;

// app/Console/Commands/RunScheduledJobCommands.php
namespace App\Console\Commands;

use App\Services\KernelService;
use Illuminate\Console\Command;

class RunScheduledJobCommands extends Command
{
    protected $signature = 'kernel:run-commands';
    protected $description = 'Ejecuta los comandos programados en la tabla scheduled_job_commands';

    public function handle(KernelService $service)
    {
        $service->executeDueCommands();
        $this->info('Comandos programados ejecutados.');
    }
}

