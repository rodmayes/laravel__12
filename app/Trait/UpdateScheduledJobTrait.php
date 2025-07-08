<?php

namespace App\Trait;

use App\Models\ScheduledJob;

trait UpdateScheduledJobTrait
{
    public function setScheduledJobStatus(string $job_id, string $status = 'success', ?string $output = null): void
    {
        ScheduledJob::where('job_id', $job_id)
            ->update([
                'output' => $output,
                'executed_at' => now(),
                'status' => $status,
            ]);
    }
}
