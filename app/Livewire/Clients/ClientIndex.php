<?php

namespace App\Livewire\Clients;

use App\Exports\AllClientsExport;
use App\Exports\ClientsReportExport;
use App\Imports\ClientsReportImport;
use App\Models\City;
use App\Models\Client;
use App\Models\User;
use App\Services\Clients\ClientServiceWebCazador;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

#[Layout('components.layouts.app')]
class ClientIndex extends Component
{
    use Toast;
    use WithFileUploads;
    use WithPagination;

    private const EDIT_DEFAULT_SCORE = 50;

    private const EDIT_DATE_FORMATS = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d'];

    protected ClientServiceWebCazador $clientService;

    /** Opciones de formulario (tipos documento, cliente, etc.) */
    public array $documentTypes = [];

    public array $clientTypes = [];

    public array $sources = [];

    public array $statuses = [];

    /** Modal edición cliente */
    public bool $showEditModal = false;

    public ?int $editingClientId = null;

    public string $create_mode = 'dni';

    public string $name = '';

    public string $phone = '';

    public string $document_type = 'DNI';

    public string $document_number = '';

    public ?string $address = null;

    public ?int $city_id = null;

    public ?string $birth_date = null;

    public string $client_type = 'comprador';

    public string $source = 'formulario_web';

    public string $status = 'nuevo';

    public int $score = self::EDIT_DEFAULT_SCORE;

    public ?string $notes = null;

    public ?int $assigned_advisor_id = null;

    /** Modal solo reasignar asesor */
    public bool $showAdvisorModal = false;

    public ?int $advisorModalClientId = null;

    public string $advisorModalClientName = '';

    public ?int $advisorModalAssignedId = null;

    /** Modal liberar cliente (eliminación lógica) */
    public bool $showReleaseModal = false;

    public ?int $releaseClientId = null;

    public string $releaseClientName = '';

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

    public function boot(ClientServiceWebCazador $clientService): void
    {
        $this->clientService = $clientService;
        $opts = $clientService->getFormOptions();
        $this->documentTypes = $opts['document_types'];
        $this->clientTypes = $opts['client_types'];
        $this->sources = $opts['sources'];
        $this->statuses = $opts['statuses'];
    }

    public function mount(): void
    {
        $this->cities = City::orderBy('name')->get(['id', 'name']);
        $this->setDefaultDateFilters();

        $user = Auth::user();
        $cacheKey = 'available_advisors_'.$user->id;
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

    public function updatedCreateMode(): void
    {
        if ($this->create_mode === 'phone') {
            $this->document_type = '';
            $this->document_number = '';
        }

        if ($this->create_mode === 'dni' && ! $this->document_type) {
            $this->document_type = 'DNI';
        }
    }

    public function openEditModal(int $clientId): void
    {
        if (! Auth::user()?->can('edit_clients')) {
            return;
        }

        $this->closeAdvisorModal();
        $this->closeReleaseModal();
        $this->resetValidation();
        $this->loadClientIntoEditForm($clientId);
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetEditFormState();
    }

    public function saveEditClient(): void
    {
        if (! Auth::user()?->can('edit_clients')) {
            return;
        }

        $this->validate($this->editValidationRules(), $this->clientService->getValidationMessages());

        if (! $this->advisorIdIsAllowed($this->assigned_advisor_id)) {
            $this->addError('assigned_advisor_id', 'El asesor seleccionado no está disponible.');

            return;
        }

        try {
            $client = Client::findOrFail($this->editingClientId);
            $data = $this->prepareEditFormData();
            $data['updated_by'] = Auth::id();
            $client->update($data);
            $this->success('Cliente actualizado correctamente.');
            $this->closeEditModal();
        } catch (\Throwable $e) {
            $this->error('No se pudo guardar: '.$e->getMessage());
        }
    }

    public function openChangeAdvisorModal(int $clientId): void
    {
        if (! Auth::user()?->can('edit_clients')) {
            return;
        }

        $client = Client::query()->select(['id', 'name', 'assigned_advisor_id'])->findOrFail($clientId);

        $this->closeEditModal();
        $this->closeReleaseModal();
        $this->resetValidation();
        $this->advisorModalClientId = $client->id;
        $this->advisorModalClientName = (string) ($client->name ?? '');
        $this->advisorModalAssignedId = $client->assigned_advisor_id;
        $this->showAdvisorModal = true;
    }

    public function closeAdvisorModal(): void
    {
        $this->showAdvisorModal = false;
        $this->advisorModalClientId = null;
        $this->advisorModalClientName = '';
        $this->advisorModalAssignedId = null;
    }

    public function saveAdvisorAssignment(): void
    {
        if (! Auth::user()?->can('edit_clients')) {
            return;
        }

        if ($this->advisorModalClientId === null) {
            return;
        }

        $this->validate([
            'advisorModalAssignedId' => 'nullable|exists:users,id',
        ]);

        if (! $this->advisorIdIsAllowed($this->advisorModalAssignedId)) {
            $this->addError('advisorModalAssignedId', 'El asesor seleccionado no está disponible.');

            return;
        }

        try {
            Client::whereKey($this->advisorModalClientId)->update([
                'assigned_advisor_id' => $this->advisorModalAssignedId,
                'updated_by' => Auth::id(),
            ]);
            $this->success('Asesor asignado actualizado.');
            $this->closeAdvisorModal();
        } catch (\Throwable $e) {
            $this->error('No se pudo actualizar el asesor: '.$e->getMessage());
        }
    }

    public function openReleaseModal(int $clientId): void
    {
        if (! Auth::user()?->can('delete_clients')) {
            return;
        }

        $this->closeEditModal();
        $this->closeAdvisorModal();
        $this->resetValidation();

        $client = Client::query()->select(['id', 'name'])->findOrFail($clientId);
        $this->releaseClientId = $client->id;
        $this->releaseClientName = (string) ($client->name ?? '');
        $this->showReleaseModal = true;
    }

    public function closeReleaseModal(): void
    {
        $this->showReleaseModal = false;
        $this->releaseClientId = null;
        $this->releaseClientName = '';
    }

    public function confirmReleaseClient(): void
    {
        if (! Auth::user()?->can('delete_clients')) {
            return;
        }

        if ($this->releaseClientId === null) {
            return;
        }

        try {
            $client = Client::findOrFail($this->releaseClientId);
            $client->delete();
            Log::info('Cliente liberado (soft delete)', [
                'client_id' => $client->id,
                'user_id' => Auth::id(),
            ]);
            $this->success('Cliente liberado correctamente.');
            $this->closeReleaseModal();
        } catch (\Throwable $e) {
            $this->error('No se pudo liberar el cliente: '.$e->getMessage());
        }
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
            if (! is_string($fullPath) || ! file_exists($fullPath)) {
                $this->error('No se pudo acceder al archivo. Sube el archivo de nuevo.');

                return;
            }

            $sheets = Excel::toArray(new ClientsReportImport, $fullPath);
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
                    ? $client->document_type.' '.$client->document_number
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
            $this->success('Procesadas '.count($this->importResults)." filas: {$registered} registrados, {$notRegistered} no registrados.");
        } catch (\Throwable $e) {
            $this->error('Error al procesar el Excel: '.$e->getMessage());
            $this->importResults = [];
        }
    }

    public function exportImportResults()
    {
        if (count($this->importResults) === 0) {
            $this->warning('No hay resultados para exportar. Procesa un archivo primero.');

            return null;
        }
        $filename = 'reporte_consulta_clientes_'.now()->format('Y-m-d_H-i-s').'.xlsx';

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
     *
     * @param  array<string, mixed>  $row
     * @param  list<string>  $possibleKeys
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

    private function loadClientIntoEditForm(int $clientId): void
    {
        $client = Client::findOrFail($clientId);

        $this->editingClientId = $client->id;
        $this->name = $client->name ?? '';
        $this->phone = $client->phone ?? '';
        $this->document_type = $client->document_type ?: 'DNI';
        $this->document_number = $client->document_number ?? '';
        $this->create_mode = $client->create_mode ?: ($client->document_number ? 'dni' : 'phone');
        $this->address = $client->address;
        $this->city_id = $client->city_id;
        $this->birth_date = $client->birth_date?->format('Y-m-d');
        $this->client_type = $client->client_type ?: 'comprador';
        $this->source = $client->source ?: 'formulario_web';
        $this->status = $client->status ?: 'nuevo';
        $this->score = $client->score ?? self::EDIT_DEFAULT_SCORE;
        $this->notes = $client->notes;
        $this->assigned_advisor_id = $client->assigned_advisor_id;
    }

    private function resetEditFormState(): void
    {
        $this->editingClientId = null;
        $this->reset([
            'create_mode', 'name', 'phone', 'document_type', 'document_number',
            'address', 'city_id', 'birth_date', 'client_type', 'source', 'status',
            'score', 'notes', 'assigned_advisor_id',
        ]);
        $this->create_mode = 'dni';
        $this->document_type = 'DNI';
        $this->client_type = 'comprador';
        $this->source = 'formulario_web';
        $this->status = 'nuevo';
        $this->score = self::EDIT_DEFAULT_SCORE;
    }

    /**
     * @return array<string, mixed>
     */
    private function editValidationRules(): array
    {
        return $this->clientService->getValidationRules($this->editingClientId, $this->create_mode);
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareEditFormData(): array
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $this->phone);
        $documentType = trim((string) $this->document_type);
        $documentType = $documentType === '' ? null : strtoupper($documentType);
        $docNum = trim((string) $this->document_number);
        if ($docNum === '') {
            $docNum = null;
        } elseif (in_array($documentType, ['DNI', 'RUC'], true)) {
            $docNum = preg_replace('/[^0-9]/', '', $docNum);
        } else {
            $docNum = strtoupper(preg_replace('/\s+/', '', $docNum));
        }

        $createMode = strtolower(trim($this->create_mode));
        if ($createMode === 'phone') {
            $documentType = null;
            $docNum = null;
        }

        return [
            'name' => trim((string) $this->name),
            'phone' => $phone,
            'document_type' => $documentType,
            'document_number' => $docNum,
            'address' => $this->address !== null && $this->address !== '' ? trim((string) $this->address) : null,
            'city_id' => $this->city_id,
            'birth_date' => $this->parseEditBirthDate(),
            'client_type' => $this->client_type,
            'source' => $this->source,
            'status' => $this->status,
            'score' => $this->score,
            'notes' => $this->notes !== null && $this->notes !== '' ? trim((string) $this->notes) : null,
            'assigned_advisor_id' => $this->assigned_advisor_id,
            'create_mode' => $createMode,
        ];
    }

    private function parseEditBirthDate(): ?string
    {
        if (! $this->birth_date) {
            return null;
        }
        foreach (self::EDIT_DATE_FORMATS as $format) {
            try {
                return Carbon::createFromFormat($format, $this->birth_date)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }
        try {
            return Carbon::parse($this->birth_date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function advisorIdIsAllowed(?int $id): bool
    {
        if ($id === null) {
            return true;
        }

        return collect($this->advisors)->pluck('id')->contains($id);
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

        if (! $applyFilters) {
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

            $filename = $filenamePrefix.'_'.now()->format('Y-m-d_H-i-s').'.xlsx';

            $this->success('Exportacion iniciada. El archivo se descargara automaticamente.');

            return Excel::download(new AllClientsExport($clients), $filename);
        } catch (\Throwable $e) {
            Log::error('Error al exportar clientes ('.$logAction.')', [
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

            $this->error('Error al exportar clientes: '.$e->getMessage());

            return null;
        }
    }
}
