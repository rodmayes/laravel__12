<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JobController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Jobs/Index', [
            'title' => 'Jobs',
            'filters' => $request->all(['search', 'field', 'order'])
        ]);
    }

    public function refreshData(Request $request)
    {
        return response()->json([
            'items' => $this->getData($request),
        ]);
    }

    public function getData(Request $request)
    {
        $items = $this->getDataQuery($request)->paginate($request->perPage ?? 20);
        return $this->transformPaginatedJobs($items);
    }

    protected function transformPaginatedJobs(LengthAwarePaginator $paginator): LengthAwarePaginator
    {
        $paginator->setCollection(
            $paginator->getCollection()->map(function ($job) {
                $payload = json_decode($job->payload);
                $data = $payload->data ?? new \stdClass();

                $commandName = $data->commandName ?? null;
                $jobType = class_basename($commandName);

                $jobDetails = [
                    'id' => $job->id,
                    'type' => $jobType,
                    'attempts' => $job->attempts,
                    'created_at' => $job->created_at,
                    'reserved_at' => $job->reserved_at,
                    'available_at' => $job->available_at,
                ];

                if (isset($data->command)) {
                    $command = @unserialize($data->command);

                    if (is_object($command)) {
                        $vars = get_object_vars($command);

                        $jobDetails['details'] = match ($jobType) {
                            'LaunchPrebookingJob' => [
                                'booking' => optional(Booking::find($vars['bookingId'] ?? null))->toArray()
                            ],
                            'UserLoginJob' => [
                                'user' => optional(User::find($vars['userId'] ?? null))->toArray()
                            ],
                            default => [],
                        };


                        foreach ($vars as $key => $value) {
                            $jobDetails['details'][$key] = $value;
                        }

                    }
                }

                return $jobDetails;
            })
        );

        return $paginator;
    }

    private function getDataQuery(Request $request)
    {
        $items = DB::table('jobs')->orderByDesc('id');

        if ($request->has('search')) {
            $items->where('available_at', 'LIKE', "%" . $request->search . "%");
        }

        // Ordenación múltiple segura
        if ($request->filled('sort')) {
            $sortArray = $request->sort;
            if (is_array($sortArray)) {
                foreach ($sortArray as $sort) {
                    if (isset($sort['field']) &&
                        in_array($sort['field'], ['id', 'available_at', 'created_at'])) {
                        $items->orderBy($sort['field'], $sort['order'] ?? 'asc');
                    }
                }
            }
        }

        return $items;
    }

    public function destroy($id)
    {
        DB::table('jobs')->where('id', $id)->delete();
        return redirect()->route('admin.jobs.index')->with('success', 'Job eliminado');
    }
}
