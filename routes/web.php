<?php

use App\Livewire\Clients\ClientRegistroMasivo;
use App\Livewire\Comiciones\CommissionList;
use App\Livewire\Projects\ProjectView;
use App\Livewire\Settings\RoleList;
use App\Livewire\Settings\UserList;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Clients\ClientList;
use App\Livewire\Clients\ClientRegistroDatero;
use App\Livewire\Projects\ProjectList;
use App\Livewire\Opportunities\OpportunityList;
use App\Livewire\Tasks\TaskList;
use App\Livewire\Reports\SalesReport;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;
use App\Livewire\Actividades\ActivityList;
use App\Livewire\Auth\RegisterDatero;
use App\Livewire\Clients\ClientListDatero;
use App\Livewire\Logs\LogViewer;
use App\Livewire\Settings\UserDatero;
use App\Livewire\Dateros\DaterosList;
use App\Livewire\Reservations\ReservationList;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Dashboard principal
    Route::get('/dashboard', Dashboard::class)->middleware('permission:view_dashboard')->name('dashboard');

    // Gestión de Clientes
    Route::get('/clients', ClientList::class)->middleware('permission:view_clients')->name('clients.index');

    Route::get('/clients-datero', ClientListDatero::class)->middleware('permission:view_clients')->name('clients.index-datero');
    Route::get('/clients/registro-masivo/{id?}', ClientRegistroMasivo::class)->middleware('permission:create_clients')->name('clients.registro-masivo');
    // Gestión de Dateros
    Route::get('/dateros', DaterosList::class)->middleware('permission:view_dateros')->name('dateros.index');
    // Gestión de Proyectos
    Route::get('/projects', ProjectList::class)->middleware('permission:view_projects')->name('projects.index');
    // Vista de Proyecto
    Route::get('/projects/{projectId}', ProjectView::class)->middleware('permission:view_projects')->name('projects.project-view');

    // Gestión de Oportunidades
    Route::get('/opportunities', OpportunityList::class)->middleware('permission:view_opportunities')->name('opportunities.index');

    // Actividades
    Route::get('/activities', ActivityList::class)->middleware('permission:view_activities')->name('activities.index');
    // Gestión de Tareas
    Route::get('/tasks', TaskList::class)->middleware('permission:view_tasks')->name('tasks.index');

    // Gestión de Reservas
    Route::get('/reservations', ReservationList::class)->middleware('permission:view_reservations')->name('reservations.index');

    // Gestión de Comisiones
    Route::get('/commissions', CommissionList::class)->middleware('permission:view_commissions')->name('commissions.index');
    Route::get('/commissions-datero', \App\Livewire\Comiciones\CommissionListDatero::class)->middleware('permission:view_commissions')->name('commissions.index-datero');

    // Reportes
    Route::get('/reports/sales', SalesReport::class)->middleware('permission:view_reports')->name('reports.sales');

    // Logs del Sistema
    Route::get('/logs', LogViewer::class)->middleware('permission:view_logs')->name('logs.index');
    

    // Gestión de Roles y Usuarios
    Route::get('/roles', RoleList::class)->middleware('permission:manage_roles')->name('roles.index');
    Route::get('/users', UserList::class)->middleware('permission:view_users')->name('users.index');
    Route::get('/users-datero', UserDatero::class)->middleware('permission:view_users')->name('users-datero');
});
Route::get('/clients/registro-datero/{id}', ClientRegistroDatero::class)->name('clients.registro-datero');
Route::get('/register-datero', RegisterDatero::class)->name('register-datero');

require __DIR__ . '/auth.php';
