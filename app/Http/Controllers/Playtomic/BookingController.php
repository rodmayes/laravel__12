<?php

namespace App\Http\Controllers\Playtomic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Playtomic\BookingStoreRequest;
use App\Http\Requests\Playtomic\BookingUpdateRequest;
use App\Http\Resources\BookingCalendarResource;
use App\Mail\PlaytomicBookingConfirmation;
use App\Models\Booking;
use App\Models\Club;
use App\Models\Resource;
use App\Models\Timetable;
use App\Models\User;
use App\Services\Playtomic\PlaytomicHttpService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    private $service;
    private $user;
    private $log;

    public function index(Request $request): Response
    {
        $clubs = Club::all();
        $players = User::withRole('playtomic')->get();
        $timetables = Timetable::all();
        $resources = Resource::all();
        return Inertia::render('Playtomic/Booking/Index', [
            'title'         => 'Bookings',
            'filters'       => $request->all(['search', 'field', 'order']),
            'clubs'           => $clubs,
            'players'         => $players,
            'timetables'       => $timetables,
            'resources'        => $resources,
        ]);
    }

    public function refreshData(Request $request)
    {
        return response()->json([
            'items' => $this->getData($request),
        ]);
    }

    public function getData(Request $request): LengthAwarePaginator
    {
        $items = $this->getDataQuery($request)->paginate($request->perPage ?? 20);
        foreach ($items as $booking) {
            $ids = array_filter(explode(',', $booking->resources));
            $resources = Resource::whereIn('id', $ids)
                ->get(['id', 'name'])
                ->map(fn($r) => ['id' => $r->id, 'name' => $r->name])
                ->toArray();

            $booking->resourcesNames = $resources;

            $ids = array_filter(explode(',', $booking->timetables));
            $timetables = Timetable::whereIn('id', $ids)
                ->get(['id', 'name'])
                ->map(fn($r) => ['id' => $r->id, 'name' => $r->name])
                ->toArray();

            $booking->timetablesNames = $timetables;
        }
        return $items;
    }

    private function getDataQuery(Request $request)
    {
        $items = Booking::query()->with('club', 'player');
        if ($request->has('search')) {
            $items->where('name', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('playtomic_id', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('booking_preference', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('club.name', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('started_at', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('timetable.name', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('player.name', 'LIKE', "%" . $request->search . "%");
        }
        if($request->has('status')){
            $items->where('status',$request->status);
        }
        if($request->has('club')){
            $items->byClub($request->club);
        }
        if($request->has('player')){
            $items->byPlayer($request->player);
        }
        if($request->has('booked')){
            if($request->booked){
                $items->booked();
            }else{
                $items->noBooked();
            }

        }

        // Ordenación múltiple
        if ($request->filled('sort')) {
            $sortArray = $request->sort;
            if (is_array($sortArray)) {
                foreach ($sortArray as $sort) {
                    if (isset($sort['field']) &&
                        in_array($sort['field'], ['id', 'name', 'playtomic_id','booking_preference','club.name', 'started_at', 'timetable.name','status','player.name', 'booked'])) {
                        $items->orderBy($sort['field'], $sort['order'] ?? 'asc');
                    }
                }
            }else{
                $items->orderBy('started_at', 'desc');
            }
        }else{
            $items->orderBy('started_at', 'desc');
        }

        return $items;
    }

    public function viewCalendar()
    {
        //abort_if(Gate::allows('hasRole', ['admin','playtomic']), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $bookings = BookingCalendarResource::collection(Booking::all())->resolve(); //notCLosed()->get()();
        return view('livewire.playtomic.booking.playtomic-calendar', compact('bookings'));
    }

    public function create(): Response
    {
        $clubs = Club::all();
        $resources = Resource::visible()->orderBy('name')->get();
        $timetables = Timetable::all();
        $players = User::withRole('playtomic')->get();
        $bookingPreference = collect(Booking::PREFERENCES);
        $status = collect(Booking::STATUS);
        $durations = collect(Booking::DURATION);
        $player = auth()->user();

        return Inertia::render('Playtomic/Booking/Create', [
            'title'         => 'Bookings',
            'clubs'         => $clubs,
            'players'       => $players,
            'timetables'    => $timetables,
            'resources'     => $resources,
            'bookingPreferences' => $bookingPreference,
            'status'        => $status,
            'durations'     => $durations,
            'player'         => $player
        ]);
    }

    public function store(BookingStoreRequest $request)
    {
        try{
            $user = auth()->user();
            $club = Club::find($request->club_id);
            $booking = Booking::create([
                'club_id' => $club->id,
                'resources' => implode(",",$request->resources),
                'timetables' => implode(",", $request->timetables),
                'created_by' => $user->id,
                'public' => (bool)$request->public,
                'name' => $club->name.' '.$request->started_at,
                'started_at' => Carbon::parse($request->started_at) ?: Carbon::now()->addDays((int)$club->days_min_booking),
                'player_email' => !$request->player ? $user->email : $request->player,
                'booking_preference' => $request->booking_preference
            ]);

            /*
            if(Carbon::now(env('APP_DATETIME_ZONE'))->startOfDay()->diffInDays($booking->started_at->startOfDay()) >= (int)$club->days_min_booking){
                $booking->status = Booking::STATUS_ONTIME;
            }
            else{
                $booking->status = Booking::STATUS_TIMEOUT;
            }
            */

            $booking->status = Booking::STATUS_ONTIME;

            $booking->save();

            return redirect()->route('playtomic.bookings.index')->with('success', $booking->name . ' created successfully.');
        }catch (\Exception $e){
            Log::error('Error creating Booking '. $e->getMessage(), $request->all());
            return back()->with('error', 'Error creating ' . $club->name.' '.$request->started_at . $e->getMessage());
        }
    }

    public function edit(Booking $booking): Response
    {
        $clubs = Club::all();
        $resources = Resource::visible()->orderBy('name')->get();
        $timetables = Timetable::all();
        $players = User::withRole('playtomic')->get();
        $bookingPreference = collect(Booking::PREFERENCES);
        $status = collect(Booking::STATUS);
        $durations = collect(Booking::DURATION);

        return Inertia::render('Playtomic/Booking/Edit', [
            'title'         => 'Bookings',
            'booking'       => $booking,
            'clubs'         => $clubs,
            'players'       => $players,
            'timetables'    => $timetables,
            'resources'     => $resources,
            'bookingPreferences' => $bookingPreference,
            'status'        => $status,
            'durations'     => $durations
        ]);
    }

    public function update(Booking $booking, BookingUpdateRequest $request)
    {
        try{
            $user = auth()->user();
            $club = Club::find($request->club_id);
            $booking->update([
                'club_id' => $club->id,
                'resources' => implode(",",$request->resources),
                'timetables' => implode(",", $request->timetables),
                'created_by' => $user->id,
                'public' => (bool)$request->public,
                'name' => $club->name.' '.$request->started_at,
                'started_at' => Carbon::parse($request->started_at) ?: Carbon::now()->addDays((int)$club->days_min_booking),
                'player_email' => !$request->player ? $user->email : $request->player,
                'booking_preference' => $request->booking_preference
            ]);

            if(Carbon::now(env('APP_DATETIME_ZONE'))->startOfDay()->diffInDays($booking->started_at->startOfDay()) >= (int)$club->days_min_booking){
                $booking->status = Booking::STATUS_ONTIME;
            }
            else{
                $booking->status = Booking::STATUS_TIMEOUT;
            }

            $booking->save();

            return redirect()->route('playtomic.bookings.index')->with('success', $booking->name . ' updated successfully.');
        }catch (\Exception $e){
            return back()->with('error', 'Error updating ' . $booking->name . $e->getMessage());
        }
    }

    public function destroy(Booking $booking)
    {
        try{
            $booking->delete();

            return redirect()->route('playtomic.bookings.index')->with('success', $booking->name . ' deleted successfully.');
        }catch (\Exception $e){
            return back()->with('error', 'Error deleting ' . $booking->name . $e->getMessage());
        }
    }

    public function startBooking(Booking $booking, Resource $resource, Timetable $timetable, User $user = null){
        $this->log = ['status'=> ['success' => ''], 'data' => [] ];
        $this->user = $user ?: Auth::user();
        if(!$this->user) {
            Log::error('No user found');
            return $this->log['status'] = ['fail' => 'No user found'];
        }
        $this->service = new PlaytomicHttpService($this->user->id);

        try{
            if(!$user) {
                $this->log['data'][] = 'Login attempt';
                $login_response = $this->service->login();
                if (!$login_response) {
                    Log::error('Not logged');
                    return $this->log['status'] = ['fail' => 'Not logged'];
                }
                $this->log['data'][] = 'Logged';
            }
            $this->log['data'][] = 'Booking start: ' . $booking->club->name . ' ' . $resource->name.' '.$booking->started_at->format('d-m-Y') . ' ' . $timetable->name;
            $prebooking = $this->booking($booking,  $resource, $timetable);
            $this->log['data'][] = 'Booking scheduled finish';
            if(isset($prebooking['error'])) throw new \Exception('Prebooking error '.$prebooking['error']);
            $booking->name = $booking->club->name . ' ' . $resource->name.' '.$booking->started_at->format('d-m-Y') . ' ' . $timetable->name;
            //$booking->resources = $resource->id;
            //$booking->timetables = $timetable->id;
            $booking->created_by = $this->user->id;
            $booking->log = json_encode($this->log);
            $booking->save();
        }catch(\Exception $e){
            $this->log['status'] = ['fail' => $e->getMessage()];
            $this->log['data'][] = $e->getMessage();
            Log::error($e->getMessage(), $this->log);
        }
        return $this->log;
    }

    public function booking(Booking $booking, Resource  $resource, Timetable $timetable){
        $this->log['data'][] = 'Prebooking '.$timetable->name;
        $prebooking = $this->getPreBooking($booking, $resource, $timetable);
        if(isset($prebooking['status']) && $prebooking['status'] === 'fail') return ['error' => 'Prebooking error '.$prebooking['message']];
        $this->log['data'][] = 'Payment method '.$timetable->name;
        $prebooking = $this->paymentMethodSelection($prebooking);
        if(isset($prebooking['status']) && $prebooking['status'] === 'fail') return ['error' => 'Payment method selection error '.$prebooking['message']];
        $this->log['data'][] = 'Confirmation '.$timetable->name;
        $prebooking = $this->confirmation($prebooking);
        if(isset($prebooking['status']) && $prebooking['status'] === 'fail') return ['error' => 'Confirmation error '.$prebooking['message']];
        $this->log['data'][] = 'Confirmation Match '.$timetable->name;
        $prebooking = $this->confirmationMatch($prebooking);
        if(isset($prebooking['status']) && $prebooking['status'] === 'fail') return ['error' => 'Confirmation match error '.$prebooking['message']];
        $this->log['data'][] = '  Booking end: ' . $booking->name . ' ' . $resource->name.' '.$booking->started_at->format('d-m-Y') . ' ' . $timetable->name . ' Do it!';
        if (!isset($prebooking['error'])) {
            Mail::to($this->user)->send(new PlaytomicBookingConfirmation($booking, $resource, $timetable, $prebooking));
            $this->log['data'][] = 'Mail sent to ' . $this->user->email;
        }
        return $prebooking;
    }

    public function getPreBooking(Booking $booking, Resource $resource, Timetable $timetable)
    {
        if($this->service->login()){
            try{
                $response = $this->service->preBooking($booking, $resource, $timetable);
                if(isset($response['status']) && $response['status'] === 'fail') {
                    $this->log['data'][] = 'Prebooking error: '. $response['message'];
                    Log::error('Prebooking error: '.$timetable->name.' '.$response['message']);
                    return ['status' => 'fail', 'message' => 'Prebooking error: '.$timetable->name.' '.$response['message']];
                }
                $this->log['data'][] = 'Prebooking Ok';
                return $response;
            }catch(\Exception $e){
                $this->log['data'][] = 'Prebooking error: '. $timetable->name.' '.$e->getMessage();
                Log::error('Prebooking error: '.$timetable->name.' '.$e->getMessage());
                return ['status' => 'fail', 'message' => 'Prebooking error: '.$timetable->name.' '.$e->getMessage()];
            }
        }else return ['status' => 'fail', 'message' => 'No logged'];
    }

    public function paymentMethodSelection($prebooking)
    {
        try{
            $response = $this->service->paymentMethodSelection($prebooking);
            if(isset($response['status']) && $response['status'] === 'fail') {
                $this->log['data'][] = 'Payment method error '. $response['message'];
                Log::error('Payment method error '.$response['message']);
                return ['status' => 'fail', 'message' => 'Payment method error ' . $response['message']];
            }
            $this->log['data'][] = 'Payment method Ok';
            return $response;
        }catch(\Exception $e){
            $this->log['data'][] = 'Payment method error '. $e->getMessage();
            Log::error('Payment method error '.$e->getMessage());
            return ['status' => 'fail', 'message' => 'Payment method error '.$e->getMessage()];
        }
    }

    public function confirmation($prebooking)
    {
        try{
            $response = $this->service->confirmation($prebooking['payment_intent_id']);
            if(isset($response['status']) && $response['status'] === 'fail') {
                $this->log['data'][] = 'Confirmation error'. $response['message'];
                Log::error('Confirmation error ' . $response['message']);
                return ['status' => 'fail', 'message' => 'Confirmation error ' . $response['message']];
            }
            $this->log['data'][] = 'Confirmation Ok';
            return $response;
        }catch (\Exception $e) {
            $this->log['data'][] = 'Confirmation error'. $e->getMessage();
            Log::error('Confirmation error '.$e->getMessage());
            return ['status' => 'fail', 'message' => 'Confirmation error '.$e->getMessage()];
        }
    }

    public function confirmationMatch($prebooking)
    {
        try{
            $response = $this->service->confirmationMatch($prebooking['cart']['item']['cart_item_data']['match_id']);
            if(isset($response['status']) && $response['status'] === 'fail') {
                $this->log['data'][] = 'Confirmation match error'. $response['message'];
                Log::error('Confirmation match error ' . $response['message']);
                return ['status' => 'fail', 'message' => 'Confirmation match error ' . $response['message']];
            }
            $this->log['data'][] = 'Confirmation match Ok';
            return $response;
        }catch (\Exception $e) {
            $this->log['data'][] = 'Confirmation match error'. $e->getMessage();
            Log::error('Confirmation match error '.$e->getMessage());
            return ['status' => 'fail', 'message' => 'Confirmation match error '.$e->getMessage()];
        }
    }

    public function toggleBooked(Booking $booking){
        try{
            $booking->toggleBooked();
            return back()->with('success', $booking->name . ' booked status changed successfully.');
        }catch (\Exception $e){
            return back()->with('error', 'Error status changed ' . $booking->name . $e->getMessage());
        }
    }
}
