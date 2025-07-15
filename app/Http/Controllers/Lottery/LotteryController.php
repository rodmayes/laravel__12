<?php
namespace App\Http\Controllers\Lottery;

use App\Http\Controllers\Controller;
use App\Jobs\getLotteryNumbersJob;
use App\Mail\sendLotteryNumbersMailable;
use App\Models\LotteryResults;
use App\Services\LotteryService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LotteryController extends Controller
{
    public function resultsIndex(Request $request): Response
    {
        return Inertia::render('Lottery/Results/Index', [
            'title'   => 'Results',
            'filters' => $request->only(['search', 'field', 'order']),
            'results' => $this->getDataResults($request),
        ]);
    }

    public function resultsRefresh(Request $request)
    {
        return response()->json([
            'results' => $this->getDataResults($request),
        ]);
    }

    public function getDataResults(Request $request): LengthAwarePaginator
    {
        return $this->getDataQuery($request)->paginate($request->perPage ?? 20);
    }

    private function getDataQuery(Request $request)
    {
        $results = LotteryResults::query();

        if ($request->has('search')) {
            $results->where(function ($query) use ($request) {
                $query->where('date_at', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('numbers', 'LIKE', "%" . $request->search . "%");
            });
        }

        // Ordenación múltiple
        if ($request->filled('sort')) {
            $sortArray = json_decode($request->sort, true);
            if (is_array($sortArray)) {
                foreach ($sortArray as $sort) {
                    if (isset($sort['field'], $sort['order']) &&
                        in_array($sort['field'], ['id', 'date_at', 'numbers'])) {
                        $results->orderBy($sort['field'], $sort['order']);
                    }
                }
            }
        }

        return $results;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'date_at' => 'required',
                'numbers' => 'required|array',
                'complementary' => 'required|integer|between:1,49',
            ]);

            $validated['date_at'] = Carbon::parse($validated['date_at'])->addDay()->toDateString(); // "2025-06-15"
            sort($validated['numbers']);
            $validated['numbers'][] = $request->complementary;

            // Validar que sea un array con exactamente 6 números
            throw_if(!is_array($validated['numbers']) || count($validated['numbers']) !== 7,  'Numbers must be an array of 6 elements.');

            $result = LotteryResults::create([
                'date_at' => $validated['date_at'],
                'numbers' => json_encode($validated['numbers'])
            ]);
            DB::commit();
            return back()->with('success', $result->date_at. ' created successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            throw new \Exception('Error creating ' . $request->date_at->format('d-m-Y') . ': ' . $th->getMessage());
        }
    }

    public function update(Request $request, LotteryResults $result)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'date_at' => 'required',
                'numbers' => 'required|array',
                'complementary' => 'required|integer|between:1,49',
            ]);

            $validated['date_at'] = Carbon::parse($validated['date_at'])->addDay()->toDateString(); // "2025-06-15"
            $validated['numbers'][] = $request->complementary;

            // Validar que sea un array con exactamente 6 números
            throw_if(!is_array($validated['numbers']) || count($validated['numbers']) !== 7,  'Numbers must be an array of 6 elements.');

            $result->update([
                'date_at' => $validated['date_at'],
                'numbers' => json_encode($validated['numbers'])
            ]);
            DB::commit();
            return back()->with('success', $result->date_at. ' updated successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            throw new \Exception('Error updating ' . $result->date_at->format('d-m-Y') . ': ' . $th->getMessage());
        }
    }

    public function destroy(LotteryResults $result)
    {
        try {
            $result->delete();
            return back()->with('success', $result->date_at . ' deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Error deleting ' . $result->date_at . $th->getMessage());

        }
    }

    public function importResults(Request $request): RedirectResponse
    {
        $service = new LotteryService();
        try{
            $file = $request->file('results_file_import');

            if (!$file || !$file->isValid()) {
                return back()->with('error', 'Invalid file upload');
            }

            $service->importResults($file);
            return back()->with('success', 'Results has been imported successfully');
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return back()->with('error', 'Import unsuccessfully: '.$e->getMessage());
        }
    }

    public function combinationsIndex(Request $request): Response
    {
        return Inertia::render('Lottery/Combinations/Index', [
            'title'   => 'Combinations',
            'filters' => $request->only(['search', 'field', 'order'])
        ]);
    }

    public function makeMagikNumbers(Request $request){
        try{
            $uuid = (string) Str::uuid();
            getLotteryNumbersJob::dispatch($request->excludedNumbers ?? [], 10, $uuid);

            return response()->json(['uuid' => $uuid]);

        }catch(\Exception $e){
            Log::error($e->getMessage());
            return back()->with('error', 'Generate magik numbers unsuccessfully: '.$e->getMessage());
        }
    }

    public function sendMailWithCombinations(Request $request): RedirectResponse
    {
        throw_if(!$request->combinations, 'No combinations found');

        Mail::to(env('MAIL_FROM_ADDRESS','rodmayes@gmail.com'))->send(new sendLotteryNumbersMailable($request->combinations));
        return back()->with('success','Mail combinations sent successfully.');
    }
}

