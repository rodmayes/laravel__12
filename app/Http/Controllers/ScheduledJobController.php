<?php

namespace App\Http\Controllers;

use App\Models\ScheduledJob;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Queue;
use Illuminate\Http\Request;

class ScheduledJobController extends Controller
{
    public function index(Request $request)
    {

        return inertia('ScheduledJobs/Index', [
            'title' => 'Scheduled Jobs',
            'filters'       => $request->all(['search', 'field', 'order']),
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
        return $this->getDataQuery($request)->paginate($request->perPage ?? 20);
    }

    private function getDataQuery(Request $request)
    {
        $items = ScheduledJob::query()->with('schedulable');
        if ($request->has('search')) {
            $items->where('schedule_type', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('job_type', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('payload', 'LIKE', "%" . $request->search . "%");
        }

        // Ordenación múltiple
        if ($request->filled('sort')) {
            $sortArray = $request->sort;//json_decode($request->sort, true);
            if (is_array($sortArray)) {
                foreach ($sortArray as $sort) {
                    if (isset($sort['field']) &&
                        in_array($sort['field'], ['id', 'schedule_type', 'job_type','payload'])) {
                        $items->orderBy($sort['field'], $sort['order'] ?? 'asc');
                    }
                }
            }else{
                $items->orderBy('name');
            }
        }else{
            $items->orderBy('name');
        }

        return $items;
    }

    public function show(ScheduledJob $scheduledJob)
    {
        return inertia('ScheduledJobs/Show', [
            'job' => $scheduledJob,
        ]);
    }

    public function update(Request $request, ScheduledJob $scheduledJob)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,running,completed,failed,cancelled',
            'executed_at' => 'nullable|date',
            'payload' => 'nullable|array',
            'scheduled_at' => 'nullable|date',
        ]);

        $scheduledJob->update($validated);

        return back()->with('success', 'Job updated successfully.');
    }

    public function cancel(ScheduledJob $scheduledJob)
    {
        try {
            Queue::connection()->delete($scheduledJob->job_id);
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors('No se ha podido cancelar el job.');
        }

        $scheduledJob->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        $scheduledJob->cancel();

        return back()->with('success', 'Job cancelled successfully.');
    }

    public function executed(ScheduledJob $scheduledJob)
    {
        $scheduledJob->update(['status' => 'executed', 'executed_at' => now()]);
        $scheduledJob->cancel();

        return back()->with('success', 'Job executed successfully.');
    }

    public function destroy(ScheduledJob $scheduledJob)
    {
        $scheduledJob->delete();

        return back()->with('success', 'Job deleted successfully.');
    }
}
