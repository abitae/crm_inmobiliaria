<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientsReportExport implements FromArray, WithHeadings
{
    /** @var array<int, array{row_number: int, status: string, document?: string, name?: string, assigned_advisor?: string, created_by?: string}> */
    private array $results;

    public function __construct(array $results)
    {
        $this->results = $results;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->results as $r) {
            $rows[] = [
                $r['row_number'],
                $r['status'] === 'registrado' ? 'Registrado' : 'No registrado',
                $r['document'] ?? '-',
                $r['name'] ?? '-',
                $r['assigned_advisor'] ?? '-',
                $r['created_by'] ?? '-',
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            'Nº Fila',
            'Estado',
            'Documento',
            'Nombre',
            'Asesor asignado',
            'Creado por',
        ];
    }
}
