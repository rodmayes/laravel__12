<?php

namespace App\Http\Controllers\Playtomic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Playtomic\ClubStoreRequest;
use App\Http\Requests\Playtomic\ClubUpdateRequest;
use App\Http\Requests\Playtomic\ClubIndexRequest;
use App\Models\Club;
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
            'items'         => $this->getDataResults($request)
        ]);
    }

    public function resultsRefresh(Request $request)
    {
        return response()->json([
            'items' => $this->getDataResults($request),
        ]);
    }

    public function getDataResults(Request $request): LengthAwarePaginator
    {
        return $this->getDataQuery($request)->paginate($request->perPage ?? 20);
    }

    private function getDataQuery(Request $request)
    {
        $clubs = Club::query();
        if ($request->has('search')) {
            $clubs->where('name', 'LIKE', "%" . $request->search . "%");
            $clubs->orWhere('playtomic_id', 'LIKE', "%" . $request->search . "%");
            $clubs->orWhere('timetable_summer', 'LIKE', "%" . $request->search . "%");
            $clubs->orWhere('booking_hour', 'LIKE', "%" . $request->search . "%");
            $clubs->orWhere('days_min_booking', 'LIKE', "%" . $request->search . "%");
        }
        // Ordenación múltiple
        if ($request->filled('sort')) {
            $sortArray = json_decode($request->sort, true);
            if (is_array($sortArray)) {
                foreach ($sortArray as $sort) {
                    if (isset($sort['field'], $sort['order']) &&
                        in_array($sort['field'], ['id', 'name', 'playtomic_id','timetable_summer','booking_hour','days_min_booking'])) {
                        $clubs->orderBy($sort['field'], $sort['order']);
                    }
                }
            }
        }

        return $clubs;
    }

    public function create()
    {
        return view('playtomic.club.create');
    }

    public function edit(Club $club)
    {
        return view('playtomic.club.edit', compact('club'));
    }

    public function show(Club $club)
    {
        $club->load('resources');
        return view('playtomic.club.show', compact('club'));
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

    public function update(ClubUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $club = Club::findOrFail($id);
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
}
