<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ScheduledJob;
use App\Services\Playtomic\PlaytomicBookingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingJobScheduler
{
    public function reschedule(Booking $booking)
    {
        // 1️⃣ Eliminar jobs de la cola (pendientes) y scheduled_jobs
        $this->deletePendingJobsForBooking($booking);

        // 3️⃣ Reprogramar jobs con lógica actualizada
        $service = new PlaytomicBookingService($booking->player);
        $service->processSingleBooking($booking);
        activity()
            ->performedOn($booking)
            ->causedBy(Auth::user())
            ->withProperties(['model' => $booking->toArray()])
            ->log('Booking - recreating jobs');
    }

    private function deletePendingJobsForBooking(Booking $booking): void
    {
        DB::table('jobs')
            ->where('queue', 'default')
            ->where('payload', 'like', '%bookingId";i:' . $booking->id . ';%')
            ->delete();

        $booking->scheduledJobs()->delete();

        // 🧹 2. Eliminar jobs anteriores
        //ScheduledJob::where('booking_id', $booking->id)->delete();
        activity()
            ->performedOn($booking)
            ->causedBy(Auth::user())
            ->withProperties(['model' => $booking->toArray()])
            ->log('Booking - deleted jobs');
    }
}
