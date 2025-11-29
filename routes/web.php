<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\{
    TicketController,
    TicketAssignmentController,
    TicketCommentController,
    DashboardController,
    TicketAttachmentController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('tickets.index');
});

Route::get('/', function () {
    return view('welcome'); //  landing page
});
Auth::routes();

Route::middleware('auth')->group(function () {

    // Tickets CRUD
    Route::resource('tickets', TicketController::class);

    // Generación IA
    Route::post('tickets/{ticket}/ai', [TicketController::class, 'generateAi'])
        ->name('tickets.ai');

    // Asignación de tickets
    Route::get('tickets/{ticket}/assign', [TicketAssignmentController::class, 'assignForm'])
        ->name('tickets.assign.form');
    Route::post('tickets/{ticket}/assign', [TicketAssignmentController::class, 'assign'])
        ->name('tickets.assign');

    // Comentarios de tickets
    Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])
        ->name('tickets.comments.store');

    // Adjuntos de tickets
    Route::post('/tickets/{ticket}/attachments', [TicketAttachmentController::class, 'store'])
        ->name('tickets.attachments.store');

    Route::delete('/tickets/{ticket}/attachments/{attachment}', [TicketAttachmentController::class, 'destroy'])
        ->name('tickets.attachments.destroy');

    // Descargar adjunto
    Route::get('/attachments/{attachment}/download', [TicketAttachmentController::class, 'download'])
        ->name('attachments.download');

    /*
    |--------------------------------------------------------------------------
    |   RUTAS PARA KANBAN
    |--------------------------------------------------------------------------
    */

    // Vista Kanban (solo IT y Manager)
    Route::get('/tickets/kanban', [TicketController::class, 'kanban'])
        ->name('tickets.kanban')
        ->middleware('can:viewKanban,App\Models\Ticket');

    // Movimiento de ticket entre columnas
    Route::patch('/tickets/{ticket}/move', [TicketController::class, 'move'])
        ->name('tickets.move');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    // Gestión de usuarios — solo autenticados
    Route::resource('users', UserController::class)
        ->except(['show']);

    // Toggle activo / inactivo para usuarios
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
        ->name('users.toggle-active');

    // Calificación de tickets
    Route::post('tickets/{ticket}/rate', [TicketController::class, 'rate'])
        ->name('tickets.rate');
        
});
