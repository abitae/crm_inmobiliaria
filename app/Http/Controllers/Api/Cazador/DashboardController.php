<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ApiResponse;

    public function stats(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->unauthorizedResponse('Usuario no autenticado');
        }

        $scopeKey = ($user->isAdmin() || $user->isLider()) ? 'all' : 'own';
        $cacheKey = 'cazador_dashboard_stats_' . $user->id . '_' . $scopeKey;

        try {
            $stats = Cache::remember($cacheKey, 300, function () use ($user, $scopeKey) {
                $clientsQuery = Client::query();
                if ($scopeKey === 'own') {
                    $clientsQuery->where('assigned_advisor_id', $user->id);
                }

                $daterosQuery = User::bySingleRole('datero');
                if ($scopeKey === 'own') {
                    $daterosQuery->where('lider_id', $user->id);
                }

                $reservationsQuery = Reservation::query();
                if ($scopeKey === 'own') {
                    $reservationsQuery->where('advisor_id', $user->id);
                }

                return [
                    'clients' => [
                        'total' => (clone $clientsQuery)->count(),
                        'by_status' => (clone $clientsQuery)
                            ->selectRaw('status, count(*) as count')
                            ->groupBy('status')
                            ->pluck('count', 'status'),
                        'by_type' => (clone $clientsQuery)
                            ->selectRaw('client_type, count(*) as count')
                            ->groupBy('client_type')
                            ->pluck('count', 'client_type'),
                    ],
                    'dateros' => [
                        'total' => (clone $daterosQuery)->count(),
                        'active' => (clone $daterosQuery)->where('is_active', true)->count(),
                        'inactive' => (clone $daterosQuery)->where('is_active', false)->count(),
                    ],
                    'projects' => [
                        'total' => Project::count(),
                        'with_available_units' => Project::withAvailableUnits()->count(),
                    ],
                    'reservations' => [
                        'total' => (clone $reservationsQuery)->count(),
                        'by_status' => (clone $reservationsQuery)
                            ->selectRaw('status, count(*) as count')
                            ->groupBy('status')
                            ->pluck('count', 'status'),
                        'by_payment_status' => (clone $reservationsQuery)
                            ->selectRaw('payment_status, count(*) as count')
                            ->groupBy('payment_status')
                            ->pluck('count', 'payment_status'),
                    ],
                ];
            });

            return $this->successResponse($stats, 'Estadisticas obtenidas exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener estadisticas del dashboard');
        }
    }
}
