<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ScheduledJob;
use App\Services\Playtomic\PlaytomicBookingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingJobScheduler
{
    public function reschedule(Booking $booking): void
    {
        // 🧹 1. Cancelar todos los jobs anteriores
        $this->deletePendingJobsForBooking($booking);

        // 🔁 2. Crear los nuevos jobs
        $service = new PlaytomicBookingService($booking->player);
        $service->processSingleBooking($booking);

        activity()
            ->performedOn($booking)
            ->causedBy(Auth::user())
            ->withProperties(['model' => $booking->toArray()])
            ->log('Booking - recreating jobs');
    }

    public function deletePendingJobsForBooking(Booking $booking): void
    {
        // 🗑️ Eliminar jobs de la cola (Laravel) por UUID
        $booking->scheduledJobs()->each(function ($scheduledJob) {
            DB::table('jobs')
                ->where('uuid', $scheduledJob->job_id)
                ->delete();

            $scheduledJob->delete();
        });

        activity()
            ->performedOn($booking)
            ->causedBy(Auth::user())
            ->withProperties(['model' => $booking->toArray()])
            ->log('Booking - deleted jobs');
    }
}
