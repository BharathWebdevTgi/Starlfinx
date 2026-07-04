<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
	LoginController, DashboardController, TimeTrackController, LeaveController
};
/*Route::get('/', function () {
    return view('welcome');
});*/



Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'loginProcess'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
	
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
	
	/* ======================= Time Track Management =============================================*/
	Route::get('/timetracks', [TimeTrackController::class, 'index'])->name('timetracks.index');
	Route::get('/timetrack/grouped-ajax',[TimeTrackController::class, 'groupedAjax'])->name('timetrack.grouped_ajax');
	Route::post('/timetrack/delete-track', [TimeTrackController::class,'deleteTrack'])->name('timetrack.delete');
	Route::get('/project-search', [TimeTrackController::class, 'projectSearch'])->name('time_tracker.project_search');
	Route::post('/timetrack/store',[TimeTrackController::class, 'store'])->name('timetrack.store');
	//Route::post('/timetrack/validate', [TimeTrackController::class, 'validateTimeTrack'])->name('timetrack.validate');	
	
	/* ======================= Leave Management =============================================*/
	Route::get('/leaves', [LeaveController::class, 'index'])->name('leave.index');
	Route::post('/leave/store', [LeaveController::class,'store'])->name('leave.store');
	Route::delete('/leave/delete/{id}', [LeaveController::class, 'delete'])->name('leave.delete');
});
