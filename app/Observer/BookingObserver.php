<?php

namespace App\Observer;

use App\Models\Booking;
use App\Services\BookingJobScheduler;
use App\Services\Playtomic\PlaytomicBookingService;
use Illuminate\Support\Facades\Auth;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function created(Booking $booking)
    {
        // Create single job
        (new PlaytomicBookingService($booking->player))->processSingleBooking($booking);
        activity()
            ->performedOn($booking)
            ->causedBy($booking->player)
            ->withProperties(['model' =>$booking->toArray()])
            ->log('Booking - created');
    }

    /**
     * Handle the Booking "updated" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function updated(Booking $booking)
    {
        if ($booking->isOnTime && $booking->isDirty(['started_at', 'club_id', 'resources', 'timetables', 'booking_preference', 'status', 'player_email', 'duration'])){
            app(BookingJobScheduler::class)->reschedule($booking);
            activity()
                ->performedOn($booking)
                ->causedBy($booking->player)
                ->withProperties(['model' => $booking->toArray()])
                ->log('Booking - reschedule');
        }
    }

    /**
     * Handle the Booking "deleting" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function deleting(Booking $booking)
    {
        app(BookingJobScheduler::class)->deletePendingJobsForBooking($booking->id, ['UserLoginJob', 'LaunchPrebookingJob']);

        activity()
            ->performedOn($booking)
            ->causedBy(Auth::user())
            ->withProperties(['model' => $booking->toArray()])
            ->log('Booking - deleted');
    }

    /**
     * Handle the Booking "restored" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function restored(Booking $booking)
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function forceDeleted(Booking $booking)
    {
        //
    }
}
