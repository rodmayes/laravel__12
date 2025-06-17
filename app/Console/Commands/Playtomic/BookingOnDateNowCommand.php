<?php

namespace App\Console\Commands\Playtomic;

use App\Models\Booking;
use App\Models\User;
use App\Services\Playtomic\PlaytomicBookingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BookingOnDateNowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playtomic:booking-onDate {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza las reservas de Playtomic en el momento';

    /**
     * Ejecuta el comando.
     */
    public function handle(): void
    {
        $email = $this->argument('user');

        $this->line('⏳ Iniciando proceso de reserva para: ' . $email);

        $user = User::byEmail($email)->first();

        if (!$user) {
            Log::warning("[Abort] No user found for email: {$email}");
            $this->error('❌ Usuario no encontrado');
            return;
        }

        $bookings = Booking::onDate()
            ->byPlayer($user->email)
            ->orderByDesc('started_at')
            ->get();

        if (!$bookings) {
            $this->warn("⚠ No hay reservas pendientes para {$email}");
            return;
        }

        foreach ($bookings as $booking) {
            (new PlaytomicBookingService($user))->processBookingsForUser($bookings, true);
        }

        $this->info('✅ Proceso de reservas completado');
    }
}
