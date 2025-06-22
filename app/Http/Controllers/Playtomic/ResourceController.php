<?php

namespace App\Http\Controllers\Playtomic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Playtomic\ResourceStoreRequest;
use App\Http\Requests\Playtomic\ResourceUpdateRequest;
use App\Models\Club;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ResourceController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        $clubs = Club::all();
        return Inertia::render('Playtomic/Resource/Index', [
            'title'         => 'Resources',
            'filters'       => $request->all(['search', 'field', 'order']),
            'items'         => $this->getData($request),
            'clubs'         => $clubs
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

    public function filter(Request $request): JsonResponse
    {
        $items = $this->getDataQuery($request)->get();
        return response()->json($items);
    }

    private function getDataQuery(Request $request)
    {
        $items = Resource::query()->with('club');
        if ($request->has('search')) {
            $items->where('name', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('playtomic_id', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('priority', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('club.name', 'LIKE', "%" . $request->search . "%");
        }
        if($request->has('visible')){
            $items->where('visible',$request->visible);
        }
        if($request->has('club')){
            $items->byClub($request->club);
        }

        // Ordenación múltiple
        if ($request->filled('sort')) {
            $sortArray = json_decode($request->sort, true);
            if (is_array($sortArray)) {
                foreach ($sortArray as $sort) {
                    if (isset($sort['field'], $sort['order']) &&
                        in_array($sort['field'], ['id', 'name', 'playtomic_id','priority','club.name'])) {
                        $items->orderBy($sort['field'], $sort['order']);
                    }
                }
            }
        }

        return $items;
    }

    public function store(ResourceStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $resource = Resource::create([
                'name' => $request->name,
                'playtomic_id' => $request->playtomic_id,
                'priority' => $request->priority,
                'club_id' => $request->club_id,
                'visible' => $request->visible
            ]);
            DB::commit();
            return back()->with('success', $resource->name. ' created successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error creating ' . $request->name . $th->getMessage());
        }
    }

    public function update(ResourceUpdateRequest $request, Resource $resource)
    {
        DB::beginTransaction();
        try {
            $resource->update([
                'name' => $request->name,
                'playtomic_id' => $request->playtomic_id,
                'priority' => $request->priority,
                'club_id' => $request->club_id,
                'visible' => $request->visible
            ]);
            DB::commit();
            return back()->with('success', $resource->name. ' updated successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error updating ' . $resource->name . $th->getMessage());
        }
    }

    public function destroy(Resource $resource)
    {
        try {
            $resource->delete();
            return back()->with('success', $resource->name . ' deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Error deleting ' . $resource->name . $th->getMessage());

        }
    }
    public function getList(Club $club = null)
    {
        $resources = Resource::byClub($club->id)->get();
        return response()->json($resources);
    }
}
