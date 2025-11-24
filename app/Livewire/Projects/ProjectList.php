<?php

namespace App\Livewire\Projects;

use App\Services\ProjectService;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
class ProjectList extends Component
{
    use WithPagination, WithFileUploads;

    // ==================== FILTROS Y BÚSQUEDA ====================
    public $search = '';
    public $statusFilter = '';
    public $isPublishedFilter = '';
    public $loteTypeFilter = '';
    public $stageFilter = '';
    public $locationFilter = '';
    public $withAvailableUnits = false;
    public $orderBy = 'created_at';
    public $orderDirection = 'desc';

    // ==================== ESTADOS DE MODALES ====================
    public $showFormModal = false;
    public $showDeleteModal = false;
    public $showAssignAdvisorModal = false;

    // ==================== PROYECTOS SELECCIONADOS ====================
    public $selectedProject = null;
    public $editingProject = null;

    // ==================== CAMPOS DEL FORMULARIO ====================
    public $name = '';
    public $description = '';
    public $project_type = 'lotes';
    public $is_published = false;
    public $lote_type = 'normal';
    public $stage = '';
    public $legal_status = '';
    public $address = '';
    public $district = '';
    public $province = '';
    public $region = '';
    public $country = '';
    public $ubicacion = '';
    public $start_date = '';
    public $end_date = '';
    public $delivery_date = '';
    public $status = 'activo';
    public $estado_legal = '';
    public $tipo_proyecto = '';
    public $tipo_financiamiento = '';
    public $banco = '';
    public $tipo_cuenta = '';
    public $cuenta_bancaria = '';

    // ==================== CAMPOS MULTIMEDIA ====================
    public $path_images = [];
    public $path_videos = [];
    public $path_documents = [];
    public $imageFiles = [];
    public $videoFiles = [];
    public $documentFiles = [];

    // ==================== MULTIMEDIA DE PORTADA ====================
    public $path_image_portada = '';
    public $imagePortadaFile = null;
    public $path_video_portada = '';
    public $videoPortadaFile = null;

    // ==================== ASIGNACIÓN DE ASESORES ====================
    public $selectedAdvisorId = '';
    public $isPrimaryAdvisor = false;
    public $advisorNotes = '';
    public $currentAdvisors = [];

    // ==================== DEPENDENCIAS ====================
    protected $projectService;
    public $advisors = [];

    // ==================== REGLAS DE VALIDACIÓN ====================
    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'project_type' => 'required|in:lotes',
        'is_published' => 'boolean',
        'lote_type' => 'required|in:normal,express',
        'stage' => 'required|in:preventa,lanzamiento,venta_activa,cierre',
        'legal_status' => 'required|in:con_titulo,en_tramite,habilitado',
        'address' => 'required|string|max:500',
        'district' => 'nullable|string|max:255',
        'province' => 'nullable|string|max:255',
        'region' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'ubicacion' => 'nullable|url|max:500',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'delivery_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:activo,inactivo,suspendido,finalizado',
        'estado_legal' => 'nullable|in:Derecho Posesorio,Compra y Venta,Juez de Paz,Titulo de propiedad',
        'tipo_proyecto' => 'nullable|in:propio,tercero',
        'tipo_financiamiento' => 'nullable|in:contado,financiado',
        'banco' => 'nullable|string|max:255',
        'tipo_cuenta' => 'nullable|in:cuenta corriente,cuenta vista,cuenta ahorro',
        'cuenta_bancaria' => 'nullable|string|max:255',
        'imagePortadaFile' => 'nullable|image|max:2048',
        'videoPortadaFile' => 'nullable|mimes:mp4,avi,mov,wmv|max:10240',
        'path_images.*.title' => 'required|string|max:255',
        'path_images.*.descripcion' => 'nullable|string|max:500',
        'path_videos.*.title' => 'required|string|max:255',
        'path_videos.*.descripcion' => 'nullable|string|max:500',
        'path_documents.*.title' => 'required|string|max:255',
        'path_documents.*.descripcion' => 'nullable|string|max:500',
    ];

    // ==================== MÉTODOS DE INICIALIZACIÓN ====================
    public function boot(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function mount()
    {
        $this->advisors = User::getAvailableAdvisors(Auth::user());
    }

    // ==================== MÉTODOS DE FILTRADO Y BÚSQUEDA ====================
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
    public function updatedIsPublishedFilter()
    {
        $this->resetPage();
    }
    public function updatedLoteTypeFilter()
    {
        $this->resetPage();
    }
    public function updatedStageFilter()
    {
        $this->resetPage();
    }
    public function updatedLocationFilter()
    {
        $this->resetPage();
    }
    public function updatedWithAvailableUnits()
    {
        $this->resetPage();
    }
    public function updatedOrderBy()
    {
        $this->resetPage();
    }
    public function updatedOrderDirection()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'statusFilter',
            'isPublishedFilter',
            'loteTypeFilter',
            'stageFilter',
            'locationFilter',
            'withAvailableUnits',
            'orderBy',
            'orderDirection'
        ]);
        $this->resetPage();
    }

    // ==================== MÉTODOS DE MODALES ====================
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal($projectId)
    {
        $this->editingProject = $this->projectService->getProjectById($projectId);
        if ($this->editingProject) {
            $this->fillFormFromProject($this->editingProject);
            $this->showFormModal = true;
        }
    }

    public function openDeleteModal($projectId)
    {
        $this->selectedProject = $this->projectService->getProjectById($projectId);
        $this->showDeleteModal = true;
    }

    public function openAssignAdvisorModal($projectId)
    {
        $this->selectedProject = $this->projectService->getProjectById($projectId);
        if ($this->selectedProject) {
            // Limpiar formulario anterior
            $this->reset(['selectedAdvisorId', 'isPrimaryAdvisor', 'advisorNotes']);
            
            // Obtener asesores actuales del proyecto con la relación pivot cargada
            $this->currentAdvisors = $this->selectedProject->advisors()->withPivot(['assigned_at', 'is_primary', 'notes'])->get();
            $this->showAssignAdvisorModal = true;
        }
    }

    public function closeModals()
    {
        $this->reset([
            'showFormModal',
            'showDeleteModal',
            'showAssignAdvisorModal',
            'editingProject',
            'selectedProject',
            'selectedAdvisorId',
            'isPrimaryAdvisor',
            'advisorNotes',
            'currentAdvisors'
        ]);
        $this->resetForm();
    }

    // ==================== MÉTODOS DEL FORMULARIO ====================
    public function resetForm()
    {
        $this->reset([
            'name',
            'description',
            'project_type',
            'is_published',
            'lote_type',
            'stage',
            'legal_status',
            'address',
            'district',
            'province',
            'region',
            'country',
            'ubicacion',
            'start_date',
            'end_date',
            'delivery_date',
            'status',
            'estado_legal',
            'tipo_proyecto',
            'tipo_financiamiento',
            'banco',
            'tipo_cuenta',
            'cuenta_bancaria',
            'path_image_portada',
            'path_video_portada',
            'imagePortadaFile',
            'videoPortadaFile',
            'path_images',
            'path_videos',
            'path_documents',
            'imageFiles',
            'videoFiles',
            'documentFiles'
        ]);
        $this->status = 'activo';
        $this->project_type = 'lotes';
        $this->is_published = false;
        $this->lote_type = 'normal';
    }

    public function fillFormFromProject($project)
    {
        $this->name = $project->name;
        $this->description = $project->description;
        $this->project_type = $project->project_type;
        $this->is_published = $project->is_published ?? false;
        $this->lote_type = $project->lote_type ?? 'normal';
        $this->stage = $project->stage;
        $this->legal_status = $project->legal_status;
        $this->address = $project->address;
        $this->district = $project->district;
        $this->province = $project->province;
        $this->region = $project->region;
        $this->country = $project->country;
        $this->ubicacion = $project->ubicacion;
        $this->start_date = $project->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $project->end_date?->format('Y-m-d') ?? '';
        $this->delivery_date = $project->delivery_date?->format('Y-m-d') ?? '';
        $this->status = $project->status;
        $this->estado_legal = $project->estado_legal ?? '';
        $this->tipo_proyecto = $project->tipo_proyecto ?? '';
        $this->tipo_financiamiento = $project->tipo_financiamiento ?? '';
        $this->banco = $project->banco ?? '';
        $this->tipo_cuenta = $project->tipo_cuenta ?? '';
        $this->cuenta_bancaria = $project->cuenta_bancaria ?? '';
        $this->path_image_portada = $project->path_image_portada ?? '';
        $this->path_video_portada = $project->path_video_portada ?? '';

        // Estructurar arrays multimedia
        $this->path_images = $this->ensureMultimediaArrayStructure($project->path_images ?? []);
        $this->path_videos = $this->ensureMultimediaArrayStructure($project->path_videos ?? []);
        $this->path_documents = $this->ensureMultimediaArrayStructure($project->path_documents ?? []);

        // Inicializar arrays de archivos
        $this->imageFiles = array_fill(0, count($this->path_images), null);
        $this->videoFiles = array_fill(0, count($this->path_videos), null);
        $this->documentFiles = array_fill(0, count($this->path_documents), null);
    }

    // ==================== MÉTODOS CRUD ====================
    public function createProject()
    {
        $this->validate();
        $this->processUploadedFiles();

        $data = $this->getProjectData();
        $project = $this->projectService->createProject($data);

        $this->closeModals();
        $this->dispatch('project-created');
        $this->dispatch('show-success', 'Proyecto creado exitosamente.');
    }

    public function updateProject()
    {
        $this->validate();

        if (!$this->editingProject) {
            return;
        }

        $this->processUploadedFiles();
        $data = $this->getProjectData();
        $this->projectService->updateProject($this->editingProject->id, $data);

        $this->closeModals();
        $this->dispatch('project-updated');
        $this->dispatch('show-success', 'Proyecto actualizado exitosamente.');
    }

    public function deleteProject()
    {
        if (!$this->selectedProject) {
            return;
        }

        $this->projectService->deleteProject($this->selectedProject->id);
        $this->closeModals();
        $this->dispatch('project-deleted');
        session()->flash('message', 'Proyecto eliminado exitosamente.');
    }

    public function assignAdvisor()
    {
        $this->validate([
            'selectedAdvisorId' => 'required|exists:users,id',
            'advisorNotes' => 'nullable|string|max:1000',
        ]);

        if (!$this->selectedProject || !$this->selectedAdvisorId) {
            $this->dispatch('show-error', 'Error: Proyecto o asesor no válido.');
            return;
        }

        try {
            $this->projectService->assignAdvisor(
                $this->selectedProject->id,
                $this->selectedAdvisorId,
                $this->isPrimaryAdvisor,
                $this->advisorNotes
            );

            // Actualizar la lista de asesores actuales
            $this->refreshAdvisorsList();

            $this->closeModals();
            $this->dispatch('advisor-assigned');
            $this->dispatch('show-success', 'Asesor asignado exitosamente.');
        } catch (\Exception $e) {
            $this->dispatch('show-error', 'Error al asignar el asesor: ' . $e->getMessage());
        }
    }

    public function refreshAdvisorsList()
    {
        if ($this->selectedProject) {
            $this->currentAdvisors = $this->selectedProject->fresh()->advisors()->withPivot(['assigned_at', 'is_primary', 'notes'])->get();
        }
    }

    public function confirmAssignAdvisor()
    {
        if (!$this->selectedAdvisorId) {
            $this->dispatch('show-error', 'Por favor selecciona un asesor.');
            return;
        }

        // Verificar si el asesor ya está asignado
        $alreadyAssigned = collect($this->currentAdvisors)->contains('id', $this->selectedAdvisorId);
        if ($alreadyAssigned) {
            $this->dispatch('show-error', 'Este asesor ya está asignado al proyecto.');
            return;
        }

        // Asignar directamente sin confirmación
        $this->assignAdvisor();
    }

    public function removeAdvisor($advisorId)
    {
        if (!$this->selectedProject) {
            $this->dispatch('show-error', 'Error: Proyecto no válido.');
            return;
        }

        $advisor = collect($this->currentAdvisors)->firstWhere('id', $advisorId);
        if (!$advisor) {
            $this->dispatch('show-error', 'Asesor no encontrado.');
            return;
        }

        // Verificar si es el último asesor principal
        if ($advisor->pivot && $advisor->pivot->is_primary) {
            $primaryAdvisors = collect($this->currentAdvisors)->filter(function($advisor) {
                return $advisor->pivot && $advisor->pivot->is_primary;
            })->count();
            if ($primaryAdvisors <= 1) {
                $this->dispatch('show-error', 'No puedes eliminar al último asesor principal. Asigna otro asesor principal primero.');
                return;
            }
        }

        // Eliminar directamente sin confirmación
        try {
            $this->projectService->removeAdvisor($this->selectedProject->id, $advisorId);
            
            // Actualizar la lista de asesores actuales con la relación pivot cargada
            $this->currentAdvisors = $this->selectedProject->fresh()->advisors()->withPivot(['assigned_at', 'is_primary', 'notes'])->get();
            $this->showAssignAdvisorModal = false;
            $this->dispatch('show-success', 'Asesor eliminado exitosamente.');
        } catch (\Exception $e) {
            $this->dispatch('show-error', 'Error al eliminar el asesor: ' . $e->getMessage());
        }
    }

    // ==================== MÉTODOS MULTIMEDIA ====================
    public function removeImagePortada()
    {
        $this->reset(['path_image_portada', 'imagePortadaFile']);
    }

    public function removeVideoPortada()
    {
        $this->reset(['path_video_portada', 'videoPortadaFile']);
    }

    public function addImage()
    {
        $this->path_images[] = ['title' => '', 'path' => '', 'descripcion' => '', 'type' => ''];
        $this->imageFiles[] = null;
    }

    public function removeImage($index)
    {
        unset($this->path_images[$index], $this->imageFiles[$index]);
        $this->path_images = array_values($this->path_images);
        $this->imageFiles = array_values($this->imageFiles);
    }

    public function addVideo()
    {
        $this->path_videos[] = ['title' => '', 'path' => '', 'descripcion' => '', 'type' => ''];
        $this->videoFiles[] = null;
    }

    public function removeVideo($index)
    {
        unset($this->path_videos[$index], $this->videoFiles[$index]);
        $this->path_videos = array_values($this->path_videos);
        $this->videoFiles = array_values($this->videoFiles);
    }

    public function addDocument()
    {
        $this->path_documents[] = ['title' => '', 'path' => '', 'descripcion' => '', 'type' => ''];
        $this->documentFiles[] = null;
    }

    public function removeDocument($index)
    {
        unset($this->path_documents[$index], $this->documentFiles[$index]);
        $this->path_documents = array_values($this->path_documents);
        $this->documentFiles = array_values($this->documentFiles);
    }

    // ==================== MÉTODOS PRIVADOS ====================
    private function getProjectData(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'project_type' => $this->project_type,
            'is_published' => $this->is_published,
            'lote_type' => $this->lote_type,
            'stage' => $this->stage,
            'legal_status' => $this->legal_status,
            'address' => $this->address,
            'district' => $this->district,
            'province' => $this->province,
            'region' => $this->region,
            'country' => $this->country,
            'ubicacion' => $this->ubicacion,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'delivery_date' => $this->delivery_date,
            'status' => $this->status,
            'estado_legal' => $this->estado_legal,
            'tipo_proyecto' => $this->tipo_proyecto,
            'tipo_financiamiento' => $this->tipo_financiamiento,
            'banco' => $this->banco,
            'tipo_cuenta' => $this->tipo_cuenta,
            'cuenta_bancaria' => $this->cuenta_bancaria,
            'path_image_portada' => $this->path_image_portada,
            'path_video_portada' => $this->path_video_portada,
            'path_images' => $this->path_images,
            'path_videos' => $this->path_videos,
            'path_documents' => $this->path_documents,
        ];
    }

    private function ensureMultimediaArrayStructure($array): array
    {
        if (!is_array($array)) {
            return [];
        }

        return array_map(function ($item) {
            if (is_array($item)) {
                return [
                    'title' => $item['title'] ?? '',
                    'path' => $item['path'] ?? '',
                    'descripcion' => $item['descripcion'] ?? '',
                    'type' => $item['type'] ?? ''
                ];
            }

            return [
                'title' => '',
                'path' => $item,
                'descripcion' => '',
                'type' => ''
            ];
        }, $array);
    }

    private function processUploadedFiles(): void
    {
        // Procesar portada de imagen
        if ($this->imagePortadaFile) {
            $path = $this->imagePortadaFile->store('projects/portadas', 'public');
            $this->path_image_portada = '/storage/' . $path;
        }

        // Procesar portada de video
        if ($this->videoPortadaFile) {
            $path = $this->videoPortadaFile->store('projects/portadas', 'public');
            $this->path_video_portada = '/storage/' . $path;
        }

        // Procesar archivos multimedia
        $this->processMultimediaFiles('image', $this->imageFiles, $this->path_images);
        $this->processMultimediaFiles('video', $this->videoFiles, $this->path_videos);
        $this->processMultimediaFiles('document', $this->documentFiles, $this->path_documents);
    }

    private function processMultimediaFiles(string $type, array $files, array &$paths): void
    {
        $folder = match ($type) {
            'image' => 'projects/images',
            'video' => 'projects/videos',
            'document' => 'projects/documents',
            default => 'projects/files'
        };

        foreach ($files as $index => $file) {
            if ($file && isset($paths[$index]) && $paths[$index]['title']) {
                $path = $file->store($folder, 'public');
                $paths[$index]['path'] = '/storage/' . $path;
            }
        }
    }

    // ==================== MÉTODOS DE RENDERIZADO ====================
    public function render()
    {
        $filters = $this->buildFilters();
        $projects = $this->projectService->getAllProjects(15, $filters);

        return view('livewire.projects.project-list', compact('projects'));
    }

    private function buildFilters(): array
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFilter,
            'is_published' => $this->isPublishedFilter,
            'lote_type' => $this->loteTypeFilter,
            'stage' => $this->stageFilter,
            'order_by' => $this->orderBy,
            'order_direction' => $this->orderDirection,
        ];

        if ($this->locationFilter) {
            $filters['location'] = ['region' => $this->locationFilter];
        }

        if ($this->withAvailableUnits) {
            $filters['with_available_units'] = true;
        }

        return $filters;
    }

    public function viewProject($projectId)
    {
        $this->selectedProject = $this->projectService->getProjectById($projectId);
        return redirect()->route('projects.project-view', $projectId);
    }

    // ==================== MÉTODOS ADICIONALES ====================
    public function exportProjects()
    {
        $filters = $this->buildFilters();
        $projects = $this->projectService->getAllProjects(1000, $filters); // Exportar más proyectos

        // Aquí puedes implementar la lógica de exportación
        // Por ejemplo, generar CSV, Excel, PDF, etc.

        $this->dispatch('show-info', 'Exportación iniciada. Los archivos estarán disponibles pronto.');
    }
}
