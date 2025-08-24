<?php

namespace App\Livewire\Projects;

use App\Services\ProjectService;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ProjectList extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $stageFilter = '';
    public $locationFilter = '';
    public $withAvailableUnits = false;
    public $orderBy = 'created_at';
    public $orderDirection = 'desc';
    public $showFormModal = false;
    public $showDeleteModal = false;
    public $showAssignAdvisorModal = false;
    public $selectedProject = null;
    public $editingProject = null;

    // Form fields
    public $name = '';
    public $description = '';
    public $project_type = '';
    public $stage = '';
    public $legal_status = '';
    public $address = '';
    public $district = '';
    public $province = '';
    public $region = '';
    public $country = '';
    public $latitude = '';
    public $longitude = '';
    public $total_units = 0;
    public $start_date = '';
    public $end_date = '';
    public $delivery_date = '';
    public $status = 'activo';

    // Multimedia fields
    public $path_images = [];
    public $path_videos = [];
    public $path_documents = [];

    // File upload properties
    public $imageFiles = [];
    public $videoFiles = [];
    public $documentFiles = [];

    // Advisor assignment
    public $selectedAdvisorId = '';
    public $isPrimaryAdvisor = false;
    public $advisorNotes = '';

    protected $projectService;
    public $advisors = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'project_type' => 'required|in:lotes,casas,departamentos,oficinas,mixto',
        'stage' => 'required|in:preventa,lanzamiento,venta_activa,cierre',
        'legal_status' => 'required|in:con_titulo,en_tramite,habilitado',
        'address' => 'required|string|max:500',
        'district' => 'nullable|string|max:255',
        'province' => 'nullable|string|max:255',
        'region' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'total_units' => 'required|integer|min:1',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'delivery_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:activo,inactivo,suspendido,finalizado',
        'path_images.*.type' => 'nullable|string|max:100',
        'path_videos.*.type' => 'nullable|string|max:100',
        'path_documents.*.type' => 'nullable|string|max:100',
        'imageFiles.*' => 'nullable|image|max:2048', // 2MB max
        'videoFiles.*' => 'nullable|mimes:mp4,avi,mov,wmv|max:10240', // 10MB max
        'documentFiles.*' => 'nullable|mimes:pdf,doc,docx|max:5120', // 5MB max
    ];

    public function boot(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function mount()
    {
        $this->advisors = User::getAdvisorsAndAdmins();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
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
        $this->search = '';
        $this->statusFilter = '';
        $this->typeFilter = '';
        $this->stageFilter = '';
        $this->locationFilter = '';
        $this->withAvailableUnits = false;
        $this->orderBy = 'created_at';
        $this->orderDirection = 'desc';
        $this->resetPage();
    }

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
        $this->showAssignAdvisorModal = true;
    }

    public function closeModals()
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->showAssignAdvisorModal = false;
        $this->resetForm();
        $this->editingProject = null;
        $this->selectedProject = null;
        $this->selectedAdvisorId = '';
        $this->isPrimaryAdvisor = false;
        $this->advisorNotes = '';
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->project_type = '';
        $this->stage = '';
        $this->legal_status = '';
        $this->address = '';
        $this->district = '';
        $this->province = '';
        $this->region = '';
        $this->country = '';
        $this->latitude = '';
        $this->longitude = '';
        $this->total_units = 0;
        $this->start_date = '';
        $this->end_date = '';
        $this->delivery_date = '';
        $this->status = 'activo';
        $this->path_images = [];
        $this->path_videos = [];
        $this->path_documents = [];
        $this->imageFiles = [];
        $this->videoFiles = [];
        $this->documentFiles = [];
        $this->selectedAdvisorId = '';
        $this->isPrimaryAdvisor = false;
        $this->advisorNotes = '';
    }

    public function fillFormFromProject($project)
    {
        $this->name = $project->name;
        $this->description = $project->description;
        $this->project_type = $project->project_type;
        $this->stage = $project->stage;
        $this->legal_status = $project->legal_status;
        $this->address = $project->address;
        $this->district = $project->district;
        $this->province = $project->province;
        $this->region = $project->region;
        $this->country = $project->country;
        $this->latitude = $project->latitude;
        $this->longitude = $project->longitude;
        $this->total_units = $project->total_units;
        $this->start_date = $project->start_date ? $project->start_date->format('Y-m-d') : '';
        $this->end_date = $project->end_date ? $project->end_date->format('Y-m-d') : '';
        $this->delivery_date = $project->delivery_date ? $project->delivery_date->format('Y-m-d') : '';
        $this->status = $project->status;
        $this->path_images = $project->path_images ?? [];
        $this->path_videos = $project->path_videos ?? [];
        $this->path_documents = $project->path_documents ?? [];
    }

    public function createProject()
    {
        $this->validate();

        // Procesar archivos subidos
        $this->processUploadedFiles();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'project_type' => $this->project_type,
            'stage' => $this->stage,
            'legal_status' => $this->legal_status,
            'address' => $this->address,
            'district' => $this->district,
            'province' => $this->province,
            'region' => $this->region,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'total_units' => $this->total_units,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'delivery_date' => $this->delivery_date,
            'status' => $this->status,
            'path_images' => $this->path_images,
            'path_videos' => $this->path_videos,
            'path_documents' => $this->path_documents,
        ];

        $this->projectService->createProject($data);

        $this->closeModals();
        $this->dispatch('project-created');
        session()->flash('message', 'Proyecto creado exitosamente.');
    }

    public function updateProject()
    {
        $this->validate();

        if (!$this->editingProject) {
            return;
        }

        // Procesar archivos subidos
        $this->processUploadedFiles();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'project_type' => $this->project_type,
            'stage' => $this->stage,
            'legal_status' => $this->legal_status,
            'address' => $this->address,
            'district' => $this->district,
            'province' => $this->province,
            'region' => $this->region,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'total_units' => $this->total_units,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'delivery_date' => $this->delivery_date,
            'status' => $this->status,
            'path_images' => $this->path_images,
            'path_videos' => $this->path_videos,
            'path_documents' => $this->path_documents,
        ];

        $this->projectService->updateProject($this->editingProject->id, $data);

        $this->closeModals();
        $this->dispatch('project-updated');
        session()->flash('message', 'Proyecto actualizado exitosamente.');
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
        if (!$this->selectedProject || !$this->selectedAdvisorId) {
            return;
        }

        $this->projectService->assignAdvisor(
            $this->selectedProject->id,
            $this->selectedAdvisorId,
            $this->isPrimaryAdvisor,
            $this->advisorNotes
        );

        $this->closeModals();
        $this->dispatch('advisor-assigned');
        session()->flash('message', 'Asesor asignado exitosamente.');
    }

    public function updateUnitCounts($projectId)
    {
        $this->projectService->updateUnitCounts($projectId);
        $this->dispatch('unit-counts-updated');
        session()->flash('message', 'Conteo de unidades actualizado.');
    }

    // Métodos para manejar campos multimedia
    public function addImage()
    {
        $this->path_images[] = [
            'type' => '',
            'path' => '',
            'name' => ''
        ];
        $this->imageFiles[] = null;
    }

    public function removeImage($index)
    {
        unset($this->path_images[$index]);
        unset($this->imageFiles[$index]);
        $this->path_images = array_values($this->path_images);
        $this->imageFiles = array_values($this->imageFiles);
    }

    public function addVideo()
    {
        $this->path_videos[] = [
            'type' => '',
            'path' => '',
            'name' => ''
        ];
        $this->videoFiles[] = null;
    }

    public function removeVideo($index)
    {
        unset($this->path_videos[$index]);
        unset($this->videoFiles[$index]);
        $this->path_videos = array_values($this->path_videos);
        $this->videoFiles = array_values($this->videoFiles);
    }

    public function addDocument()
    {
        $this->path_documents[] = [
            'type' => '',
            'path' => '',
            'name' => ''
        ];
        $this->documentFiles[] = null;
    }

    public function removeDocument($index)
    {
        unset($this->path_documents[$index]);
        unset($this->documentFiles[$index]);
        $this->path_documents = array_values($this->path_documents);
        $this->documentFiles = array_values($this->documentFiles);
    }

    // Método para procesar archivos subidos
    private function processUploadedFiles()
    {
        // Procesar imágenes
        foreach ($this->imageFiles as $index => $file) {
            if ($file && isset($this->path_images[$index]) && $this->path_images[$index]['type']) {
                $path = $file->store('projects/images', 'public');
                $this->path_images[$index]['path'] = '/storage/' . $path;
                $this->path_images[$index]['name'] = $file->getClientOriginalName();
            }
        }

        // Procesar videos
        foreach ($this->videoFiles as $index => $file) {
            if ($file && isset($this->path_videos[$index]) && $this->path_videos[$index]['type']) {
                $path = $file->store('projects/videos', 'public');
                $this->path_videos[$index]['path'] = '/storage/' . $path;
                $this->path_videos[$index]['name'] = $file->getClientOriginalName();
            }
        }

        // Procesar documentos
        foreach ($this->documentFiles as $index => $file) {
            if ($file && isset($this->path_documents[$index]) && $this->path_documents[$index]['type']) {
                $path = $file->store('projects/documents', 'public');
                $this->path_documents[$index]['path'] = '/storage/' . $path;
                $this->path_documents[$index]['name'] = $file->getClientOriginalName();
            }
        }
    }

    public function render()
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFilter,
            'type' => $this->typeFilter,
            'stage' => $this->stageFilter,
            'order_by' => $this->orderBy,
            'order_direction' => $this->orderDirection,
        ];

        // Agregar filtro de ubicación solo si está seleccionado
        if ($this->locationFilter) {
            $filters['location'] = [
                'region' => $this->locationFilter
            ];
        }

        // Agregar filtro de unidades disponibles
        if ($this->withAvailableUnits) {
            $filters['with_available_units'] = true;
        }

        $projects = $this->projectService->getAllProjects(15, $filters);

        return view('livewire.projects.project-list', [
            'projects' => $projects
        ]);
    }
}
