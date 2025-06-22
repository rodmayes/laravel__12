<?php

namespace App\Http\Controllers\Playtomic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Playtomic\ClubStoreRequest;
use App\Http\Requests\Playtomic\ClubUpdateRequest;
use App\Http\Requests\Playtomic\ClubIndexRequest;
use App\Models\Club;
use App\Models\Resource;
use App\Services\Playtomic\PlaytomicHttpService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ClubController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Playtomic/Club/Index', [
            'title'         => 'Clubs',
            'filters'       => $request->all(['search', 'field', 'order']),
            'items'         => $this->getData($request)
        ]);
    }

    public function refresData(Request $request)
    {
        return response()->json([
            'items' => $this->getData($request),
        ]);
    }

    public function getData(Request $request): LengthAwarePaginator
    {
        return $this->getDataQuery($request)->paginate($request->perPage ?? 20);
    }

    private function getDataQuery(Request $request)
    {
        $items = Club::query();
        if ($request->has('search')) {
            $items->where('name', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('playtomic_id', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('timetable_summer', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('booking_hour', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('days_min_booking', 'LIKE', "%" . $request->search . "%");
        }
        // Ordenación múltiple
        if ($request->filled('sort')) {
            $sortArray = json_decode($request->sort, true);
            if (is_array($sortArray)) {
                foreach ($sortArray as $sort) {
                    if (isset($sort['field'], $sort['order']) &&
                        in_array($sort['field'], ['id', 'name', 'playtomic_id','timetable_summer','booking_hour','days_min_booking'])) {
                        $items->orderBy($sort['field'], $sort['order']);
                    }
                }
            }
        }

        return $items;
    }

    public function store(ClubStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $club = Club::create([
                'name' => $request->name,
                'playtomic_id' => $request->playtomic_id,
                'days_min_booking' => $request->days_min_booking,
                'timetable_summer' => $request->timetable_summer,
                'booking_hour' => $request->booking_hour,
            ]);
            DB::commit();
            return back()->with('success', $club->name. ' created successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error creating ' . $request->name . $th->getMessage());
        }
    }

    public function update(ClubUpdateRequest $request, Club $club)
    {
        DB::beginTransaction();
        try {
            $club->update([
                'name' => $request->name,
                'playtomic_id' => $request->playtomic_id,
                'days_min_booking' => $request->padays_min_bookingssword,
                'timetable_summer' => $request->timetable_summer,
                'booking_hour' => $request->booking_hour,
            ]);
            DB::commit();
            return back()->with('success', $club->name. ' updated successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error updating ' . $club->name . $th->getMessage());
        }
    }

    public function destroy(Club $club)
    {
        try {
            $club->delete();
            return back()->with('success', $club->name . ' deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Error deleting ' . $club->name . $th->getMessage());

        }
    }

    public function availability(Club $club)
    {
        return view('playtomic.club.availability', compact('club'));
    }

    public function getInfo(Club $club)
    {
        $info = (new PlaytomicHttpService())->getInformationClub($club);
        return response()->json($info);
    }

    public function getList()
    {
        $clubs = Club::all();
        return response()->json($clubs);
    }

    public function syncResources(Club $club){
        try {
            $service = (new PlaytomicHttpService(auth()->user()->id));
            $service->login();
            $information_club = $service->getInformationClub($club);

            if (isset($information_club['resources']))
                foreach ($information_club['resources'] as $resource) {
                    Resource::updateOrCreate(
                        [
                            'playtomic_id' => $resource['resource_id'],
                            'club_id' => $club->id
                        ],
                        [
                            'name' => $resource['name'],
                            'playtomic_id' => $resource['resource_id'],
                            'club_id' => $club->id
                        ]);
                }
            return back()->with('success', $club->name. ' resources synced successfully.');
        }catch (\Exception $e){
            return back()->with('error', $club->name. ' resources synced failed. '.$e->getMessage());
        }
    }
}
