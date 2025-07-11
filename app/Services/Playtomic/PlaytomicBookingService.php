<?php

namespace App\Services\Playtomic;

use App\Jobs\Playtomic\LaunchPrebookingJob;
use App\Jobs\Playtomic\UserLoginJob;
use App\Models\Booking;
use App\Models\Resource;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PlaytomicBookingService
{
    private User $userPlaytomic;
    private string $timezone;

    public function __construct(User $user)
    {
        $this->userPlaytomic = $user;
        $this->timezone = env('APP_DATETIME_ZONE', 'Europe/Andorra');
    }

    public function processBookingsForUser($bookings, $runNow = false): void
    {
        Log::debug("[Start] Booking process for user: {$this->userPlaytomic->email}");

        foreach ($bookings as $booking) {
            $this->processSingleBooking($booking, $runNow);
        }

        Log::info('Booking job scheduling finished');
    }

    public function processSingleBooking(Booking $booking, bool $runNow = false): void
    {
        Log::debug("[Start] Booking process for single booking");
        $executionDate = $booking->executionDate;
        Log::debug('Execution date: '.$executionDate);
        $executionDate1MinutBefore = $executionDate->copy()->subMinute();

        // Crear el job (sin UUID manual)
        $loginJob = new UserLoginJob($this->userPlaytomic->id);

        // Dispatch y captura del job encolado (con UUID de Laravel)
        $runNow
            ? dispatch($loginJob)
            : dispatch($loginJob->delay($executionDate1MinutBefore));

        // Guardar el ScheduledJob usando el UUID real
        $booking->scheduledJobs()->create([
            'job_id'         => (string) Str::uuid(),
            'job_type'       => get_class($loginJob),
            'scheduled_for'  => $executionDate1MinutBefore,
            'payload'        => ['user_id' => $this->userPlaytomic->id, 'action' => 'login'],
            'status'         => 'pending'
        ]);

        $this->enqueuePrebookingJobs($booking, $executionDate, $runNow);
    }


    protected function enqueuePrebookingJobs(Booking $booking, $executionDate, $runNow): void
    {
        $timetableIds = array_map('intval', explode(',', $booking->timetables));
        $timetables = Timetable::whereIn('id', $timetableIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $timetableIds) . ')')
            ->get();

        $resourceIds = array_map('intval', explode(',', $booking->resources));
        $resources = Resource::whereIn('id', $resourceIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $resourceIds) . ')')
            ->get();

        $pref = $booking->booking_preference;
        $primaryItems = $pref === 'timetable' ? $timetables : $resources;
        $secondaryItems = $pref === 'timetable' ? $resources : $timetables;

        Booking::withoutEvents(function () use ($booking) {
            $booking->log = null;
            $booking->save();
        });

        $delaySeconds = 0;

        foreach ($primaryItems as $p1) {
            foreach ($secondaryItems as $p2) {
                [$resource, $timetable] = $pref === 'timetable' ? [$p2, $p1] : [$p1, $p2];

                $jobTime = match (true) {
                    $delaySeconds < 3 => $executionDate->copy()->addSeconds($delaySeconds),
                    default => $executionDate->copy()->addSeconds(2),
                };

                $job = new LaunchPrebookingJob(
                    $this->userPlaytomic->id,
                    $booking->id,
                    $resource->id,
                    $timetable->id
                );

                // Dispatch y capturar job con UUID real
                $runNow
                    ? dispatch($job)
                    : dispatch($job->delay($jobTime));

                $booking->scheduledJobs()->create([
                    'job_id'        => (string) Str::uuid(),
                    'job_type'      => get_class($job),
                    'scheduled_for' => $jobTime,
                    'payload'       => [
                        'resource_id'  => $resource->id,
                        'timetable_id' => $timetable->id,
                        'action'       => 'prebooking'
                    ],
                    'status'         => 'pending'
                ]);

                $delaySeconds++;
            }
        }
    }
}
