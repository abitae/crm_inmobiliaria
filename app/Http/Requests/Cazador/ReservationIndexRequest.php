<?php

namespace App\Http\Requests\Cazador;

use Illuminate\Foundation\Http\FormRequest;

class ReservationIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|min:2|max:100',
            'include' => 'sometimes|string|max:200',
            'status' => 'sometimes|in:activa,confirmada,cancelada,vencida,convertida_venta',
            'payment_status' => 'sometimes|in:pendiente,pagado,parcial',
            'project_id' => 'sometimes|integer|exists:projects,id',
            'client_id' => 'sometimes|integer|exists:clients,id',
            'advisor_id' => 'sometimes|integer|exists:users,id',
        ];
    }
}
