<?php

namespace App\Http\Controllers;

use App\Models\ScheduledJobCommand;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ScheduledJobCommandController extends Controller
{
    public function index(Request $request): Response
    {
        $allCommands = Artisan::all();
        $availableCommands = collect($allCommands)->map(function ($command, $name) {
            return [
                'label' => $name,
                'value' => $name,
            ];
        })->values();

        return Inertia::render('ScheduledJobCommand/Index', [
            'title' => 'Scheduler commands',
            'filters'       => $request->all(['search', 'field', 'order']),
            'items' => $this->getData($request),
            'availableCommands' => $availableCommands,
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
        $items = ScheduledJobCommand::query();
        if ($request->has('search')) {
            $items->where('command', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('parameters', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('booking_preference', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('scheduled_for', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('executed_at', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('status', 'LIKE', "%" . $request->search . "%");
            $items->orWhere('output', 'LIKE', "%" . $request->search . "%");
        }
        if($request->has('status')){
            $items->where('status',$request->status);
        }

        // Ordenación múltiple
        if ($request->filled('sort')) {
            $sortArray = json_decode($request->sort, true);
            if (is_array($sortArray)) {
                foreach ($sortArray as $sort) {
                    if (isset($sort['field'], $sort['order']) &&
                        in_array($sort['field'], ['id', 'command', 'parameters','booking_preference','scheduled_for', 'executed_at', 'status','output'])) {
                        $items->orderBy($sort['field'], $sort['order']);
                    }
                }
            }
        }

        return $items;
    }

    public function store(Request $request)
    {
        try{
            $request->validate([
                'command' => 'required|string',
                'parameters' => 'nullable|array',
                'scheduled_for' => 'required|string',
                'active' => 'boolean'
            ]);

            //$cron = $this->generateCronExpression($request->scheduled_for, $request->time);

            ScheduledJobCommand::create([
                'command' => $request->command,
                'parameters' => $request->parameters ?? [],
                'scheduled_for' => $request->scheduled_for,
                'active' => $request->active ?? true,
                'status' => 'pending',
            ]);

            return redirect()->back()->with('success', 'Comando programado creado.');
        }catch (\Exception $e){
            Log::error('Error create Command '. $e->getMessage(), [...$request->all(),'cron' => $cron ?? null]);
            return back()->with('error', 'Error creating ' . $request->command.' '. $e->getMessage());
        }
    }

    public function update(Request $request, ScheduledJobCommand $scheduledJobCommand)
    {
        try{
            $request->validate([
                'command' => 'required|string',
                'parameters' => 'nullable|array',
                'scheduled_for' => 'required|string',
                'active' => 'boolean'
            ]);

            //$cron = $this->generateCronExpression($request->scheduled_for, $request->time);

            $scheduledJobCommand->update([
                'command' => $request->command,
                'parameters' => $request->parameters ?? [],
                'scheduled_for' => $request->scheduled_for,
                'active' => $request->active ?? true,
            ]);

            return redirect()->back()->with('success', 'Comando programado actualizado.');
        }catch (\Exception $e){
            Log::error('Error updating Command '. $e->getMessage(), $request->all());
            return back()->with('error', 'Error updateing ' . $scheduledJobCommand->command.' '. $e->getMessage());
        }
    }

    public function destroy(ScheduledJobCommand $scheduledCommand)
    {
        $scheduledCommand->delete();

        return redirect()->back()->with('success', 'Comando eliminado correctamente.');
    }

    protected function generateCronExpression($schedule, $time)
    {
        [$hour, $minute] = explode(':', $time) + [0, 0];
        $hour = (int) $hour;
        $minute = (int) $minute;
        return match ($schedule) {
            'daily'                 => "$minute $hour * * *",
            'weekly'                => "$minute $hour * * 1",          // cada lunes
            'monthly'               => "$minute $hour 1 * *",          // día 1 de cada mes
            'yearly'                => "$minute $hour 1 1 *",          // 1 de enero
            'first-of-month'        => "$minute $hour 1 * *",
            'first-monday-of-month' => "$minute $hour 1-7 * 1",
            default                 => null,
        };
    }
}
