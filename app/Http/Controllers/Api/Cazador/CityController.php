<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    use ApiResponse;

    /**
     * Listar ciudades para formularios (selects, filtros).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = City::query()->orderBy('name');

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where('name', 'like', '%' . $search . '%');
            }

            $perPage = min((int) $request->get('per_page', 100), 500);
            $cities = $query->paginate($perPage);

            $items = $cities->getCollection()->map(fn (City $city) => [
                'id' => $city->id,
                'name' => $city->name,
            ]);

            return $this->successResponse([
                'cities' => $items,
                'pagination' => [
                    'current_page' => $cities->currentPage(),
                    'per_page' => $cities->perPage(),
                    'total' => $cities->total(),
                    'last_page' => $cities->lastPage(),
                    'from' => $cities->firstItem(),
                    'to' => $cities->lastItem(),
                    'links' => [
                        'first' => $cities->url(1),
                        'last' => $cities->url($cities->lastPage()),
                        'prev' => $cities->previousPageUrl(),
                        'next' => $cities->nextPageUrl(),
                    ],
                ],
            ], 'Ciudades obtenidas exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener ciudades');
        }
    }
}
