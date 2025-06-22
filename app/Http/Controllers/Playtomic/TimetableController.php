<?php

namespace App\Http\Controllers\Playtomic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Playtomic\ClubUpdateRequest;
use App\Http\Requests\Playtomic\TimetableIndexRequest;
use App\Http\Requests\Playtomic\TimetableStoreRequest;
use App\Http\Requests\Playtomic\TimetableUpdateRequest;
use App\Models\Club;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Inertia\Inertia;

class TimetableController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Playtomic/TimeTable/Index', [
            'title'         => 'Timestables',
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
        $items = Timetable::query();
        if ($request->has('search')) {
            $items->where('name', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('playtomic_id', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('playtomic_id_summer', 'LIKE', "%" . $request->search . "%");
        }
        // Ordenación múltiple
        if ($request->filled('sort')) {
            $sortArray = json_decode($request->sort, true);
            if (is_array($sortArray)) {
                foreach ($sortArray as $sort) {
                    if (isset($sort['field'], $sort['order']) &&
                        in_array($sort['field'], ['id', 'name', 'playtomic_id','playtomic_id_summer'])) {
                        $items->orderBy($sort['field'], $sort['order']);
                    }
                }
            }
        }

        return $items;
    }

    public function store(TimetableStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $timestable = Timetable::create([
                'name' => $request->name,
                'playtomic_id' => $request->playtomic_id,
                'playtomic_id_summer' => $request->playtomic_id_summer
            ]);
            DB::commit();
            return back()->with('success', $timestable->name. ' created successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error creating ' . $request->name . $th->getMessage());
        }
    }

    public function update(TimetableUpdateRequest $request, Timetable $timetable)
    {
        DB::beginTransaction();
        try {
            $timetable->update([
                'name' => $request->name,
                'playtomic_id' => $request->playtomic_id,
                'playtomic_id_summer' => $request->playtomic_id_summer
            ]);
            DB::commit();
            return back()->with('success', $timetable->name. ' updated successfully.');
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

    public function getList()
    {
        return response()->json(Timetable::all());
    }
}
