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
use App\Http\Controllers\ScheduledJobCommandController;
use App\Http\Controllers\Auth\TelegramLoginController;
use App\Http\Controllers\Administration\JobController;

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

Route::get('/job/status/{uuid}', [\App\Http\Controllers\JobController::class, 'status']);

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
Route::get('/telegram/confirm/{token}', [TelegramLoginController::class, 'confirm'])->name('telegram.login.confirm');
Route::post('/telegram/webhook', [TelegramLoginController::class, 'webhook'])->name('telegram.webhook');

Route::get('/telegram/confirm-login/{token}', function ($token) {
    $user = User::where('login_token', $token)->firstOrFail();
    $user->update(['login_confirmed_at' => now()]);

    auth()->login($user);
    return redirect()->route('dashboard')->with('success', 'Login confirmado.');
})->name('telegram.login.confirm');


Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('', [JobController::class, 'index'])->name('index');
        Route::post('refreshData', [JobController::class, 'refreshData'])->name('refreshData');
        Route::delete('{id}', [JobController::class, 'destroy'])->name('delete');
    });
});


require __DIR__.'/auth.php';
