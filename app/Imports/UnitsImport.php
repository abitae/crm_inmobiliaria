<?php

namespace App\Imports;

use App\Models\Unit;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Collection;

class UnitsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use SkipsFailures;

    protected $project;
    protected $errors = [];
    protected $successCount = 0;
    protected $errorCount = 0;
    protected $processedUnits = []; // Rastrear combinaciones de manzana y unit_number procesadas

    public function __construct(Project $project)
    {
        $this->project = $project;
        // Precargar combinaciones de unit_manzana y unit_number existentes en la base de datos
        $existingUnits = $project->units()->select('unit_manzana', 'unit_number')->get();
        foreach ($existingUnits as $unit) {
            $key = $this->getUnitKey($unit->unit_manzana, $unit->unit_number);
            $this->processedUnits[] = $key;
        }
    }
    
    /**
     * Genera una clave única para la combinación de manzana y número de unidad
     */
    private function getUnitKey($manzana, $unitNumber)
    {
        // Normalizar: convertir null a string vacío y trim
        $manzana = $manzana ? trim($manzana) : '';
        $unitNumber = trim($unitNumber);
        return strtolower($manzana . '|' . $unitNumber);
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Mapear columnas del Excel a campos de la base de datos
            $mapping = [
                'numero_unidad' => 'unit_number',
                'manzana' => 'unit_manzana',
                'tipo' => 'unit_type',
                'torre' => 'tower',
                'bloque' => 'block',
                'piso' => 'floor',
                'area' => 'area',
                'dormitorios' => 'bedrooms',
                'banos' => 'bathrooms',
                'estacionamientos' => 'parking_spaces',
                'cocheras' => 'storage_rooms',
                'area_balcon' => 'balcony_area',
                'area_terraza' => 'terrace_area',
                'area_jardin' => 'garden_area',
                'precio_base' => 'base_price',
                'precio_total' => 'total_price',
                'descuento_porcentaje' => 'discount_percentage',
                'comision_porcentaje' => 'commission_percentage',
                'estado' => 'status',
                'notas' => 'notes'
            ];

            $unitData = [
                'project_id' => $this->project->id,
                'created_by' => auth()->id() ?? 1,
                'updated_by' => auth()->id() ?? 1,
            ];

            // Normalizar las claves del array (convertir a minúsculas y reemplazar espacios/guiones)
            $normalizedRow = [];
            foreach ($row as $key => $value) {
                $normalizedKey = strtolower(trim(str_replace([' ', '-', '_'], '_', $key)));
                $normalizedRow[$normalizedKey] = $value;
            }

            // Mapear y validar datos
            foreach ($mapping as $excelColumn => $dbField) {
                $normalizedColumn = strtolower(str_replace([' ', '-'], '_', $excelColumn));
                
                if (isset($normalizedRow[$normalizedColumn]) && $normalizedRow[$normalizedColumn] !== '' && $normalizedRow[$normalizedColumn] !== null) {
                    $value = is_string($normalizedRow[$normalizedColumn]) ? trim($normalizedRow[$normalizedColumn]) : $normalizedRow[$normalizedColumn];
                    
                    // Validaciones específicas por campo
                    switch ($dbField) {
                        case 'unit_type':
                            // Solo aceptar lote
                            if (strtolower($value) !== 'lote') {
                                throw new \Exception("Tipo de unidad inválido: '{$value}'. Solo se acepta tipo 'lote'.");
                            }
                            $value = 'lote';
                            break;
                            
                        case 'status':
                            $validStatuses = ['disponible', 'reservado', 'vendido', 'transferido', 'cuotas'];
                            if (!in_array(strtolower($value), $validStatuses)) {
                                throw new \Exception("Estado inválido: '{$value}'. Valores válidos: " . implode(', ', $validStatuses));
                            }
                            $value = strtolower($value);
                            break;
                            
                        case 'area':
                        case 'base_price':
                        case 'total_price':
                        case 'discount_percentage':
                        case 'commission_percentage':
                        case 'balcony_area':
                        case 'terrace_area':
                        case 'garden_area':
                            if (!is_numeric($value)) {
                                throw new \Exception("El campo '{$excelColumn}' debe ser numérico");
                            }
                            $value = (float) $value;
                            break;
                            
                        case 'floor':
                        case 'bedrooms':
                        case 'bathrooms':
                        case 'parking_spaces':
                        case 'storage_rooms':
                            if (!is_numeric($value)) {
                                throw new \Exception("El campo '{$excelColumn}' debe ser numérico");
                            }
                            $value = (int) $value;
                            break;
                    }
                    
                    $unitData[$dbField] = $value;
                }
            }

            // Validar campos requeridos
            if (empty($unitData['unit_number'])) {
                throw new \Exception("El número de unidad es requerido");
            }
            
            // Obtener manzana (puede ser null o vacío)
            $manzana = isset($unitData['unit_manzana']) ? $unitData['unit_manzana'] : null;
            $unitKey = $this->getUnitKey($manzana, $unitData['unit_number']);
            
            // Verificar si ya existe la combinación de manzana y unit_number en la base de datos o en esta importación
            if (in_array($unitKey, $this->processedUnits)) {
                $manzanaText = $manzana ? "Manzana {$manzana}" : "Sin manzana";
                throw new \Exception("La combinación {$manzanaText} - Unidad {$unitData['unit_number']} ya existe en este proyecto o está duplicada en el archivo");
            }
            
            // Agregar a la lista de procesados para evitar duplicados en el mismo archivo
            $this->processedUnits[] = $unitKey;
            
            if (empty($unitData['unit_type'])) {
                $unitData['unit_type'] = 'lote';
            }
            
            if (empty($unitData['area'])) {
                throw new \Exception("El área es requerida");
            }
            
            if (empty($unitData['base_price'])) {
                throw new \Exception("El precio base es requerido");
            }
            
            if (empty($unitData['total_price'])) {
                throw new \Exception("El precio total es requerido");
            }
            
            if (empty($unitData['status'])) {
                $unitData['status'] = 'disponible';
            }

            // Establecer valores por defecto para lotes
            $unitData['tower'] = null;
            $unitData['block'] = null;
            $unitData['floor'] = null;
            $unitData['bedrooms'] = 0;
            $unitData['bathrooms'] = 0;
            $unitData['parking_spaces'] = 0;
            $unitData['storage_rooms'] = 0;
            $unitData['balcony_area'] = 0;
            $unitData['terrace_area'] = 0;
            $unitData['garden_area'] = 0;

            // Calcular descuento y comisión
            $discountAmount = 0;
            if (isset($unitData['discount_percentage']) && $unitData['discount_percentage'] > 0) {
                $discountAmount = ($unitData['total_price'] * $unitData['discount_percentage']) / 100;
            } else {
                $unitData['discount_percentage'] = 0;
            }
            $unitData['discount_amount'] = $discountAmount;

            $commissionAmount = 0;
            if (isset($unitData['commission_percentage']) && $unitData['commission_percentage'] > 0) {
                $commissionAmount = ($unitData['total_price'] * $unitData['commission_percentage']) / 100;
            } else {
                $unitData['commission_percentage'] = 0;
            }
            $unitData['commission_amount'] = $commissionAmount;

            $unitData['final_price'] = $unitData['total_price'] - $discountAmount;

            $this->successCount++;
            
            return new Unit($unitData);
            
        } catch (\Exception $e) {
            $this->errorCount++;
            $this->errors[] = $e->getMessage();
            return null;
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'numero_unidad' => 'required',
            'area' => 'required|numeric|min:0.01',
            'precio_base' => 'required|numeric|min:0.01',
            'precio_total' => 'required|numeric|min:0.01',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'numero_unidad.required' => 'El número de unidad es requerido',
            'area.required' => 'El área es requerida',
            'area.numeric' => 'El área debe ser numérico',
            'precio_base.required' => 'El precio base es requerido',
            'precio_base.numeric' => 'El precio base debe ser numérico',
            'precio_total.required' => 'El precio total es requerido',
            'precio_total.numeric' => 'El precio total debe ser numérico',
        ];
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * @return int
     */
    public function getErrorCount(): int
    {
        return $this->errorCount;
    }
}
