<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Reservation;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    use ApiResponse;

    public function sync(Request $request)
    {
        $since = $request->get('since');
        if (!$since) {
            return $this->errorResponse('El parametro since es obligatorio', null, 422);
        }

        try {
            $sinceDate = Carbon::parse($since);
        } catch (\Exception $e) {
            return $this->errorResponse('Formato de fecha invalido', null, 422);
        }

        $user = Auth::user();
        $isAdminOrLider = $user->isAdmin() || $user->isLider();

        $clientsQuery = Client::where('updated_at', '>', $sinceDate);
        if (!$isAdminOrLider) {
            $clientsQuery->where(function ($q) use ($user) {
                $q->where('assigned_advisor_id', $user->id)
                    ->orWhere('created_by', $user->id);
            });
        }

        $reservationsQuery = Reservation::where('updated_at', '>', $sinceDate);
        if (!$isAdminOrLider) {
            $reservationsQuery->where('advisor_id', $user->id);
        }

        $projectsQuery = Project::where('updated_at', '>', $sinceDate);

        return $this->successResponse([
            'clients' => $clientsQuery->get(),
            'reservations' => $reservationsQuery->get(),
            'projects' => $projectsQuery->get(),
            'sync_timestamp' => now()->toIso8601String(),
        ], 'Sincronizacion completada');
    }
}
