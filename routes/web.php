<?php

use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ScheduledJobController;
use App\Http\Controllers\ScheduledJobCommandController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
Route::get('/', function () {
    return redirect('login');
});
*/

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'users'         => (int) User::count(),
        'roles'         => (int) Role::count(),
        'permissions'   => (int) Permission::count(),
    ]);
})->middleware(['auth', 'verified', 'login.confirmed'])->name('dashboard');

Route::middleware(['auth', 'verified', 'login.confirmed'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/user', UserController::class)->except('create', 'show', 'edit');
    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');

    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');

    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');

});

Route::get('/form', function () {
    return Inertia::render('SakaiForm');
});

Route::get('/button', function () {
    return Inertia::render('SakaiButton');
});

Route::get('/list', function () {
    return Inertia::render('SakaiList');
});

 Route::get('/', function () {
     return Inertia::render('Welcome', [
         'canLogin' => Route::has('login'),
         'canRegister' => Route::has('register'),
         'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
         'phpVersion' => PHP_VERSION,
     ]);
 });

 /*
Route::get('/dashboard', function () {
     return Inertia::render('Dashboard');
 })->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
 */

// SCHEDULED JOBS
Route::prefix('scheduled-jobs')->name('scheduled-jobs.')->middleware(['auth', 'login.confirmed'])->group(function () {
    Route::get('/', [ScheduledJobController::class, 'index'])->name('index');
    Route::post('getData', [ScheduledJobController::class, 'getData'])->name('getData');
    Route::post('refreshData', [ScheduledJobController::class, 'refreshData'])->name('refreshData');
    Route::post('/{scheduledJob}/cancel', [ScheduledJobController::class, 'cancel'])->name('cancel');
    Route::put('/{scheduledJob}', [ScheduledJobController::class, 'update'])->name('update');
    Route::delete('/{scheduledJob}', [ScheduledJobController::class, 'destroy'])->name('destroy');
});

// SCHEDULED JOBS COMMANDS
Route::prefix('scheduled-job-commands')->name('scheduled-job-commands.')->middleware(['auth', 'login.confirmed'])->group(function () {
    Route::get('/', [ScheduledJobCommandController::class, 'index'])->name('index');
    Route::post('/', [ScheduledJobCommandController::class, 'store'])->name('store');
    Route::post('getData', [ScheduledJobCommandController::class, 'getData'])->name('getData');
    Route::post('refreshData', [ScheduledJobCommandController::class, 'refreshData'])->name('refreshData');
    Route::put('/{scheduledJobCommand}/toogle-active', [ScheduledJobCommandController::class, 'toogleActive'])->name('toggle-active');
    Route::put('/{scheduledJobCommand}', [ScheduledJobCommandController::class, 'update'])->name('update');
    Route::delete('/{scheduledJobCommand}', [ScheduledJobCommandController::class, 'destroy'])->name('destroy');
});

// TELEGRAM
Route::get('/telegram/confirm/{token}', function ($token) {
    $user = \App\Models\User::where('login_token', $token)->firstOrFail();

    // Autenticar al usuario
    \Illuminate\Support\Facades\Auth::login($user);

    // Limpia el token (opcional)
    $user->confirmation_token = null;
    $user->save();

    return redirect()->intended('/');
})->name('telegram.confirm');

Route::get('/telegram/confirm-login/{token}', function ($token) {
    $user = User::where('login_token', $token)->firstOrFail();
    $user->update(['login_confirmed_at' => now()]);

    auth()->login($user);
    return redirect()->route('dashboard')->with('success', 'Login confirmado.');
})->name('telegram.login.confirm');

Route::post('/telegram/webhook', function (\Illuminate\Http\Request $request) {
    $data = $request->all();
    \Illuminate\Support\Facades\Log::info('Mensaje recibido de Telegram:', $data);

    // Puedes actuar en función del mensaje recibido
    // $data['message']['text'], $data['message']['chat']['id'], etc.

    return response()->noContent();
});


require __DIR__.'/auth.php';
