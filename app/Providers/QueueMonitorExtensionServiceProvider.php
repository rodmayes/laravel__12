<?php

namespace App\Providers;

use Illuminate\Queue\Jobs\DatabaseJob;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobQueued;
use romanzipp\QueueMonitor\Models\Monitor;
use Illuminate\Support\Facades\Event;

class QueueMonitorExtensionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen(JobQueued::class, function (JobQueued $event) {
            if (! $event->job instanceof DatabaseJob) {
                return; // Solo aplicamos esto a DatabaseJob
            }

            $payload = $event->job->payload();
            $jobData = json_decode($payload, true);

            if (!isset($jobData['uuid'])) {
                return;
            }

            $uuid = $jobData['uuid'];
            $availableAt = $event->job->availableAt();

            Monitor::where('uuid', $uuid)->update([
                'available_at' => $availableAt ? now()->addSeconds($availableAt - time()) : now()
            ]);
        });
    }
}
