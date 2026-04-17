<?php

namespace App\Livewire\Clients;

use App\Exports\AllClientsExport;
use App\Exports\ClientsReportExport;
use App\Imports\ClientsReportImport;
use App\Models\Client;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

#[Layout('components.layouts.app')]
class ClientIndex extends Component
{
    use Toast;
    use WithPagination;
    use WithFileUploads;

    /** Búsqueda por name, document_number, phone */
    public string $search = '';

    /** Filtros */
    public string $cityFilter = '';
    public string $createdFromFilter = '';
    public string $createdToFilter = '';
    public string $createModeFilter = '';
    public string $assignedAdvisorFilter = '';
    public string $createdByFilter = '';

    public int $searchMinLength = 2;

    /** Datos para selects */
    public $cities = [];
    public $advisors = [];

    /** Importación Excel */
    public bool $showImportModal = false;
    public $importFile = null;
    /** @var array<int, array{row_number: int, status: string, document?: string, name?: string, assigned_advisor?: string, created_by?: string}> */
    public array $importResults = [];

    public function mount(): void
    {
        $this->cities = City::orderBy('name')->get(['id', 'name']);
        $this->setDefaultDateFilters();

        $user = Auth::user();
        $cacheKey = 'available_advisors_' . $user->id;
        $this->advisors = Cache::remember($cacheKey, 300, fn () => User::getAvailableAdvisors($user));
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCityFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCreatedFromFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCreatedToFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCreateModeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedAssignedAdvisorFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCreatedByFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'cityFilter',
            'createModeFilter',
            'assignedAdvisorFilter',
            'createdByFilter',
        ]);
        $this->setDefaultDateFilters();
        $this->resetPage();
    }

    public function openImportModal(): void
    {
        $this->reset(['importFile', 'importResults']);
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->reset(['importFile', 'importResults']);
    }

    public function processImport(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'importFile.required' => 'Selecciona un archivo Excel.',
            'importFile.mimes' => 'Solo se permiten archivos Excel (.xlsx, .xls).',
            'importFile.max' => 'El archivo no debe superar 10 MB.',
        ]);

        $this->importResults = [];

        try {
            $fullPath = $this->importFile->getRealPath();
            if (!is_string($fullPath) || !file_exists($fullPath)) {
                $this->error('No se pudo acceder al archivo. Sube el archivo de nuevo.');
                return;
            }

            $sheets = Excel::toArray(new ClientsReportImport(), $fullPath);
            $rows = $sheets[0] ?? [];

            $rowNumber = 1; // 1 = cabecera
            foreach ($rows as $row) {
                $rowNumber++;
                $dni = $this->getRowValue($row, ['dni_cliente', 'DNI CLIENTE', 'dni cliente']);
                $phone = $this->getRowValue($row, ['celular_cliente', 'CELULAR CLIENTE', 'celular cliente']);

                $dni = is_scalar($dni) ? trim((string) $dni) : '';
                $phone = is_scalar($phone) ? trim((string) $phone) : '';

                $client = null;
                if ($dni !== '') {
                    $client = Client::query()
                        ->where('document_number', $dni)
                        ->with(['assignedAdvisor:id,name', 'createdBy:id,name'])
                        ->first();
                }
                if ($client === null && $phone !== '') {
                    $client = Client::query()
                        ->where('phone', $phone)
                        ->with(['assignedAdvisor:id,name', 'createdBy:id,name'])
                        ->first();
                }

                if ($client === null) {
                    $this->importResults[] = [
                        'row_number' => $rowNumber,
                        'status' => 'no registrado',
                    ];
                    continue;
                }

                $document = $client->document_type && $client->document_number
                    ? $client->document_type . ' ' . $client->document_number
                    : ($client->document_number ?? '-');

                $this->importResults[] = [
                    'row_number' => $rowNumber,
                    'status' => 'registrado',
                    'document' => $document,
                    'name' => $client->name ?? '-',
                    'assigned_advisor' => $client->assignedAdvisor?->name ?? 'Sin asignar',
                    'created_by' => $client->createdBy?->name ?? '-',
                ];
            }

            $registered = count(array_filter($this->importResults, fn (array $r) => $r['status'] === 'registrado'));
            $notRegistered = count(array_filter($this->importResults, fn (array $r) => $r['status'] === 'no registrado'));
            $this->success("Procesadas " . count($this->importResults) . " filas: {$registered} registrados, {$notRegistered} no registrados.");
        } catch (\Throwable $e) {
            $this->error('Error al procesar el Excel: ' . $e->getMessage());
            $this->importResults = [];
        }
    }

    public function exportImportResults()
    {
        if (count($this->importResults) === 0) {
            $this->warning('No hay resultados para exportar. Procesa un archivo primero.');
            return null;
        }
        $filename = 'reporte_consulta_clientes_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new ClientsReportExport($this->importResults), $filename);
    }

    public function exportViewClients()
    {
        return $this->downloadClientsExport(
            applyFilters: true,
            filenamePrefix: 'clientes_vista',
            emptyMessage: 'No hay clientes para exportar con los filtros actuales.',
            logAction: 'vista'
        );
    }

    public function exportAllClients()
    {
        return $this->downloadClientsExport(
            applyFilters: false,
            filenamePrefix: 'clientes_total',
            emptyMessage: 'No hay clientes para exportar.',
            logAction: 'total'
        );
    }

    /**
     * Obtiene el valor de una fila por una de las claves posibles (cabeceras pueden variar).
     * @param array<string, mixed> $row
     * @param list<string> $possibleKeys
     */
    private function getRowValue(array $row, array $possibleKeys): mixed
    {
        foreach ($possibleKeys as $key) {
            if (array_key_exists($key, $row)) {
                $v = $row[$key];
                return $v;
            }
        }
        // Con WithHeadingRow a veces las claves se normalizan (espacios → _)
        $normalized = [];
        foreach ($row as $k => $v) {
            $normalized[strtolower(str_replace(' ', '_', (string) $k))] = $v;
        }
        foreach ($possibleKeys as $key) {
            $n = strtolower(str_replace(' ', '_', $key));
            if (array_key_exists($n, $normalized)) {
                return $normalized[$n];
            }
        }
        return null;
    }

    private function buildClientsQuery(bool $applyFilters = true): Builder
    {
        $query = Client::query()
            ->select([
                'id', 'name', 'phone', 'document_type', 'document_number', 'birth_date',
                'client_type', 'source', 'status', 'score', 'create_mode',
                'assigned_advisor_id', 'created_by', 'city_id', 'created_at',
            ])
            ->with([
                'assignedAdvisor:id,name',
                'createdBy:id,name',
                'city:id,name',
                'activities' => fn ($q) => $q->select('id', 'client_id', 'title', 'start_date')
                    ->latest('start_date')->limit(1),
            ]);

        if (!$applyFilters) {
            return $query;
        }

        $search = $this->normalizeSearch($this->search);
        $searchReady = $search !== '' && (function_exists('mb_strlen') ? mb_strlen($search) : strlen($search)) >= $this->searchMinLength;

        if ($searchReady) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($this->cityFilter !== '') {
            $query->where('city_id', $this->cityFilter);
        }

        if ($this->createdFromFilter !== '') {
            $query->whereDate('created_at', '>=', $this->createdFromFilter);
        }

        if ($this->createdToFilter !== '') {
            $query->whereDate('created_at', '<=', $this->createdToFilter);
        }

        if ($this->createModeFilter !== '') {
            $query->where('create_mode', $this->createModeFilter);
        }

        if ($this->assignedAdvisorFilter !== '') {
            $query->where('assigned_advisor_id', $this->assignedAdvisorFilter);
        }

        if ($this->createdByFilter !== '') {
            $query->where('created_by', $this->createdByFilter);
        }

        return $query;
    }

    public function render()
    {
        $clients = $this->buildClientsQuery()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.clients.client-index', [
            'clients' => $clients,
        ]);
    }

    private function normalizeSearch(string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value));
        return $value === '' ? '' : (function_exists('mb_strtolower') ? mb_strtolower($value) : strtolower($value));
    }

    private function setDefaultDateFilters(): void
    {
        $this->createdToFilter = now()->toDateString();
        $this->createdFromFilter = now()->subDays(15)->toDateString();
    }

    private function downloadClientsExport(
        bool $applyFilters,
        string $filenamePrefix,
        string $emptyMessage,
        string $logAction
    ) {
        try {
            $clients = $this->buildClientsQuery($applyFilters)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($clients->isEmpty()) {
                $this->warning($emptyMessage);
                return null;
            }

            $filename = $filenamePrefix . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            $this->success('Exportacion iniciada. El archivo se descargara automaticamente.');

            return Excel::download(new AllClientsExport($clients), $filename);
        } catch (\Throwable $e) {
            Log::error('Error al exportar clientes (' . $logAction . ')', [
                'user_id' => Auth::id(),
                'apply_filters' => $applyFilters,
                'search' => $this->search,
                'city_filter' => $this->cityFilter,
                'created_from_filter' => $this->createdFromFilter,
                'created_to_filter' => $this->createdToFilter,
                'create_mode_filter' => $this->createModeFilter,
                'assigned_advisor_filter' => $this->assignedAdvisorFilter,
                'created_by_filter' => $this->createdByFilter,
                'error' => $e->getMessage(),
            ]);

            $this->error('Error al exportar clientes: ' . $e->getMessage());
            return null;
        }
    }
}
