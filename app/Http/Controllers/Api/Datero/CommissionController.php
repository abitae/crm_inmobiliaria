<?php

namespace App\Http\Controllers\Api\Datero;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CommissionController extends Controller
{
    use ApiResponse;

    /**
     * Formatear comisión para respuesta API
     */
    protected function formatCommission(Commission $commission): array
    {
        return [
            'id' => $commission->id,
            'project' => [
                'id' => $commission->project->id ?? null,
                'name' => $commission->project->name ?? null,
            ],
            'unit' => [
                'id' => $commission->unit->id ?? null,
                'unit_number' => $commission->unit->unit_number ?? null,
            ],
            'opportunity' => [
                'id' => $commission->opportunity->id ?? null,
                'client_name' => $commission->opportunity->client->name ?? null,
            ],
            'commission_type' => $commission->commission_type,
            'base_amount' => (float) $commission->base_amount,
            'commission_percentage' => (float) $commission->commission_percentage,
            'commission_amount' => (float) $commission->commission_amount,
            'bonus_amount' => (float) $commission->bonus_amount,
            'total_commission' => (float) $commission->total_commission,
            'status' => $commission->status,
            'payment_date' => $commission->payment_date?->format('Y-m-d'),
            'payment_method' => $commission->payment_method,
            'payment_reference' => $commission->payment_reference,
            'notes' => $commission->notes,
            'approved_at' => $commission->approved_at?->format('Y-m-d H:i:s'),
            'paid_at' => $commission->paid_at?->format('Y-m-d H:i:s'),
            'created_at' => $commission->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $commission->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Listar comisiones del datero autenticado
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = min((int) $request->get('per_page', 15), 100);
            $filters = [
                'status' => $request->get('status'),
                'commission_type' => $request->get('commission_type'),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
            ];

            $query = Commission::with(['project:id,name', 'unit:id,unit_number', 'opportunity.client:id,name'])
                ->byAdvisor(Auth::id());

            // Aplicar filtros
            if (!empty($filters['status'])) {
                $query->byStatus($filters['status']);
            }

            if (!empty($filters['commission_type'])) {
                $query->byType($filters['commission_type']);
            }

            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $query->byDateRange(
                    Carbon::parse($filters['start_date'])->startOfDay(),
                    Carbon::parse($filters['end_date'])->endOfDay()
                );
            }

            // Paginar resultados
            $commissions = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Formatear comisiones
            $formattedCommissions = $commissions->map(function ($commission) {
                return $this->formatCommission($commission);
            });

            return $this->successResponse([
                'commissions' => $formattedCommissions,
                'pagination' => [
                    'current_page' => $commissions->currentPage(),
                    'per_page' => $commissions->perPage(),
                    'total' => $commissions->total(),
                    'last_page' => $commissions->lastPage(),
                    'from' => $commissions->firstItem(),
                    'to' => $commissions->lastItem(),
                ]
            ], 'Comisiones obtenidas exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener las comisiones');
        }
    }

    /**
     * Obtener una comisión específica
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $commission = Commission::with([
                'project:id,name',
                'unit:id,unit_number',
                'opportunity.client:id,name',
            ])->find($id);

            if (!$commission) {
                return $this->notFoundResponse('Comisión');
            }

            // Verificar que la comisión pertenezca al datero autenticado
            if ($commission->advisor_id !== Auth::id()) {
                return $this->forbiddenResponse('No tienes permiso para acceder a esta comisión');
            }

            return $this->successResponse(
                ['commission' => $this->formatCommission($commission)],
                'Comisión obtenida exitosamente'
            );

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener la comisión');
        }
    }

    /**
     * Obtener estadísticas de comisiones del datero
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        try {
            $userId = Auth::id();

            $stats = [
                'total' => Commission::byAdvisor($userId)->count(),
                'pendiente' => Commission::byAdvisor($userId)->pending()->count(),
                'aprobada' => Commission::byAdvisor($userId)->approved()->count(),
                'pagada' => Commission::byAdvisor($userId)->paid()->count(),
                'cancelada' => Commission::byAdvisor($userId)->cancelled()->count(),
                'total_pagado' => (float) Commission::byAdvisor($userId)->paid()->sum('total_commission'),
                'total_pendiente' => (float) Commission::byAdvisor($userId)->unpaid()->sum('total_commission'),
                'total_mes_actual' => (float) Commission::byAdvisor($userId)->thisMonth()->sum('total_commission'),
                'total_anio_actual' => (float) Commission::byAdvisor($userId)->thisYear()->sum('total_commission'),
            ];

            return $this->successResponse(['stats' => $stats], 'Estadísticas obtenidas exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener las estadísticas');
        }
    }
}

