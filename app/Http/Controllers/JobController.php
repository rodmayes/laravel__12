<?php

namespace App\Http\Controllers;

use App\Models\ScheduledJob;

class JobController extends Controller
{
    public function status($uuid)
    {
        $job = ScheduledJob::where('job_id', $uuid)->first();
        return response()->json(['status' => $job->status]); // success, failed, running...
    }
}
