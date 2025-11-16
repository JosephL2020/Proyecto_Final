<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\{
    TicketController,
    TicketAssignmentController,
    TicketCommentController,
    DashboardController
};

Route::get('/', function () {
    return redirect()->route('tickets.index');
});

Auth::routes();

Route::middleware('auth')->group(function () {

    Route::resource('tickets', TicketController::class);

    Route::post('tickets/{ticket}/ai', [TicketController::class, 'generateAi'])
        ->name('tickets.ai');

    Route::get('tickets/{ticket}/assign', [TicketAssignmentController::class, 'assignForm'])
        ->name('tickets.assign.form');

    Route::post('tickets/{ticket}/assign', [TicketAssignmentController::class, 'assign'])
        ->name('tickets.assign');

    Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])
        ->name('tickets.comments.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    Route::resource('users', \App\Http\Controllers\UserController::class)
    ->except(['show'])
    ->middleware('can:manage-users');
    
    Route::post('tickets/{ticket}/rate', [\App\Http\Controllers\TicketController::class, 'rate'])
    ->name('tickets.rate');

});
