<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Lee un Excel con cabeceras: NOMBRE CLIENTE, DNI CLIENTE, CELULAR CLIENTE.
 * Devuelve las filas como array para procesar en el componente (búsqueda y reporte).
 */
class ClientsReportImport implements ToArray, WithHeadingRow
{
    public function array(array $array): array
    {
        return $array;
    }
}
