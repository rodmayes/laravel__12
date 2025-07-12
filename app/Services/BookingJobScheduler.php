<?php

namespace App\Services;

use App\Models\Booking;
use App\Services\Playtomic\PlaytomicBookingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingJobScheduler
{
    public function reschedule(Booking $booking): void
    {
        // 🧹 1. Cancelar todos los jobs anteriores
        $this->deletePendingJobsForBooking($booking->id, ['UserLoginJob', 'LaunchPrebookingJob']);

        // 🔁 2. Crear los nuevos jobs
        $service = new PlaytomicBookingService($booking->player);
        $service->processSingleBooking($booking);

        activity()
            ->performedOn($booking)
            ->causedBy(Auth::user())
            ->withProperties(['model' => $booking->toArray()])
            ->log('Booking - recreating jobs');
    }

    function deletePendingJobsForBooking($bookingId, array $jobTypes = [])
    {
        $jobs = DB::table('jobs')->get();

        foreach ($jobs as $job) {
            $payload = json_decode($job->payload);
            $data = $payload->data ?? null;

            if (! isset($data->command)) {
                continue;
            }

            $command = @unserialize($data->command);
            if (! is_object($command)) {
                continue;
            }

            $vars = get_object_vars($command);

            if (
                isset($vars['bookingId']) &&
                (empty($jobTypes) || in_array(class_basename($data->commandName), $jobTypes))
                && $vars['bookingId'] === $bookingId
            ) {
                DB::table('jobs')->where('id', $job->id)->delete();
            }
        }
    }
}
