<?php

namespace App\Services\Playtomic;

use App\Jobs\Playtomic\LaunchPrebookingJob;
use App\Jobs\Playtomic\UserLoginJob;
use App\Models\Booking;
use App\Models\Resource;
use App\Models\ScheduledJob;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        Log::debug("[Start] Booking process for user: {$this->user->email}");

        foreach ($bookings as $booking) {
            $this->processSingleBooking($booking, $runNow);
        }

        Log::info('Booking job scheduling finished');
    }


    public function processSingleBooking(Booking $booking, bool $runNow = false): void
    {
        // 📅 Fecha de ejecución base (día de apertura de reservas)
        $executionDate = $booking->executionDate;

        $job = new UserLoginJob($this->userPlaytomic->id);

        if ($runNow) {
            Log::debug('Login Execution now: ' .now()->format('Y-m-d H:i:s'));
            dispatch($job);
        }else{
            $executionDate1MinutBefore = $executionDate->copy()->subMinute();
            Log::debug('Login Execution date: ' . $executionDate1MinutBefore);
            dispatch($job->delay($executionDate1MinutBefore));
        }

        ScheduledJob::create([
            'booking_id' => $booking->id,
            'job_class' => LaunchPrebookingJob::class,
            'scheduled_for' => $executionDate
        ]);

        $this->enqueuePrebookingJobs($booking, $executionDate, $runNow);
    }

    protected function enqueuePrebookingJobs(Booking $booking, $executionDate, $runNow): void
    {
        $timetables = Timetable::whereIn('id', explode(",", $booking->timetables))
            ->orderByRaw(DB::raw("FIELD(id, {$booking->timetables})"))
            ->get();

        $resources = Resource::whereIn('id', explode(",", $booking->resources))
            ->orderByRaw(DB::raw("FIELD(id, {$booking->resources})"))
            ->get();

        $pref = $booking->booking_preference;
        $primaryItems = $pref === 'timetable' ? $timetables : $resources;
        $secondaryItems = $pref === 'timetable' ? $resources : $timetables;

        // Log reset
        Booking::withoutEvents(function () use ($booking) {
            $booking->log = null;
            $booking->save();
        });

        // Only 2 first jobs interval execution must be 1s
        $delaySeconds = 0;

        foreach ($primaryItems as $p1) {
            foreach ($secondaryItems as $p2) {
                [$resource, $timetable] = $pref === 'timetable' ? [$p2, $p1] : [$p1, $p2];

                $job = new LaunchPrebookingJob(
                    $this->userPlaytomic->id,
                    $booking->id,
                    $resource->id,
                    $timetable->id
                );

                // Definir el tiempo de ejecución
                $jobTime = match (true) {
                    $delaySeconds < 3 => $executionDate->copy()->addSeconds($delaySeconds),
                    default => $executionDate->copy()->addSeconds(2),
                };

                if ($runNow) {
                    Log::debug('Booking Execution now: ' . now()->format('Y-m-d H:i:s'));
                    dispatch($job);
                } else {
                    Log::debug('Booking Execution date: ' . $jobTime);
                    dispatch($job->delay($jobTime));
                }

                ScheduledJob::create([
                    'booking_id' => $booking->id,
                    'job_class' => LaunchPrebookingJob::class,
                    'scheduled_for' => $jobTime
                ]);

                $delaySeconds++;
            }
        }
    }
}
