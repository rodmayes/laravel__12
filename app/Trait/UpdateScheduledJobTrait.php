<?php

namespace App\Trait;

use App\Models\ScheduledJob;

trait UpdateScheduledJobTrait
{
    public function setScheduledJobStatus(string $job_id, string $status = 'success'): void
    {
        ScheduledJob::where('job_id', $job_id)
            ->update([
                'executed_at' => now(),
                'status' => $status,
            ]);
    }
}
