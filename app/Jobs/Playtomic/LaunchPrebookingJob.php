<?php

namespace App\Jobs\Playtomic;

use App\Mail\PlaytomicBookingConfirmation;
use App\Models\Booking;
use App\Models\Resource;
use App\Models\ScheduledJob;
use App\Models\Timetable;
use App\Models\User;
use App\Services\Playtomic\PlaytomicHttpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class LaunchPrebookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $userId;
    public int $bookingId;
    public int $resourceId;
    public int $timetableId;
    public string $uuid;

    public PlaytomicHttpService $service;

    public function __construct($userId, $bookingId, $resourceId, $timetableId, ?string $uuid = null)
    {
        $this->userId = $userId;
        $this->bookingId = $bookingId;
        $this->resourceId = $resourceId;
        $this->timetableId = $timetableId;
        $this->uuid = $uuid ?? (string) Str::uuid();
    }

    public function handle(): void
    {
        $scheduled = ScheduledJob::where('job_id', $this->uuid)->first();

        if ($scheduled && $scheduled->cancelled_at) {
            Log::info("Job {$this->uuid} cancelado. No se ejecuta.");
            return;
        }

        $user = User::find($this->userId);
        $booking = Booking::find($this->bookingId);
        $resource = Resource::find($this->resourceId);
        $timetable = Timetable::find($this->timetableId);

        if (!$user || !$booking || !$resource || !$timetable) {
            $this->appendLog($booking ?? null, "[Prebooking] Invalid data - Job skipped");
            return;
        }

        $this->service = new PlaytomicHttpService($this->userId, true);
        $this->appendLog($booking, 'Start prebooking for booking ID ' . $booking->id);

        try {
            $response = $this->service->preBooking($booking, $resource, $timetable);
            Log::debug('Response prebooking', ['response' => $response]);

            if (!$response || (isset($response['status']) && $response['status'] === 'fail')) {
                $msg = $response['message'] ?? 'KO, response NULL';
                $this->appendLog($booking, "Prebooking failed: $msg");
                return;
            }

            $this->appendLog($booking, 'Prebooking OK');
            $bookingResult = $this->makeBooking($response, $booking);

            if (!isset($bookingResult['error'])) {
                Mail::to($user->email)->send(new PlaytomicBookingConfirmation($booking, $resource, $timetable, $bookingResult));
                $this->appendLog($booking, 'Email sent');
                $booking->setBooked();
            } else {
                $this->appendLog($booking, 'Booking failed: ' . $bookingResult['error']);
            }

            // ✅ Marcar como ejecutado
            if ($scheduled) {
                $scheduled->executed_at = now();
                $scheduled->save();
            }

        } catch (\Throwable $e) {
            $this->appendLog($booking, 'Job error: ' . $e->getMessage());
        }
    }

    protected function makeBooking(array $prebooking, Booking $booking): array|string
    {
        if (!isset($prebooking['payment_intent_id'])) {
            $this->appendLog($booking, 'Missing payment_intent_id');
            return ['error' => 'Missing payment_intent_id'];
        }

        try {
            $step1 = $this->service->paymentMethodSelection($prebooking);
            if ($step1['status'] === 'fail') {
                return ['error' => $step1['message']];
            }

            $step2 = $this->service->confirmation($step1['payment_intent_id']);
            if ($step2['status'] === 'fail') {
                return ['error' => $step2['message']];
            }

            $matchId = $step2['cart']['item']['cart_item_data']['match_id'] ?? null;
            if (!$matchId) {
                return ['error' => 'Missing match_id'];
            }

            $step3 = $this->service->confirmationMatch($matchId);
            if (isset($step3['status']) && $step3['status'] === 'fail') {
                return ['error' => $step3['message']];
            }

            return $step3;
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function appendLog(?Booking $booking, string $message): void
    {
        Log::info($message);

        if ($booking) {
            $logArray = json_decode($booking->log ?? '[]', true);
            $logArray[] = now()->format('Y-m-d H:i:s') . ' - ' . $message;
            $booking->log = json_encode($logArray);
            $booking->save();
        }
    }
}
