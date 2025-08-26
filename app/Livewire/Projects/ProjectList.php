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

    // Unidades del proyecto
    public $available_units = 0;
    public $reserved_units = 0;
    public $sold_units = 0;
    public $blocked_units = 0;

    // Configuración general de precios del proyecto (promedios)
    public $currency = 'PEN';
    public $base_price = 0; // Precio base promedio por unidad
    public $price_per_sqm = 0; // Precio promedio por m²
    public $discount_percentage = 0; // Descuento promedio del proyecto
    public $final_price = 0; // Precio final promedio
    
    // Opciones de pago del proyecto
    public $accepts_credit = false;
    public $accepts_cash = false;
    public $accepts_transfer = false;

    // Multimedia fields
    public $path_images = [];
    public $path_videos = [];
    public $path_documents = [];

    // File upload properties
    public $imageFiles = [];
    public $videoFiles = [];
    public $documentFiles = [];

    // Multimedia de portada
    public $path_image_portada = '';
    public $imagePortadaFile = null;
    public $path_video_portada = '';
    public $videoPortadaFile = null;

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
        'imagePortadaFile' => 'nullable|image|max:2048', // 2MB max
        'videoPortadaFile' => 'nullable|mimes:mp4,avi,mov,wmv|max:10240', // 10MB max
        'path_images.*.title' => 'required|string|max:255',
        'path_images.*.descripcion' => 'nullable|string|max:500',
        'path_videos.*.title' => 'required|string|max:255',
        'path_videos.*.descripcion' => 'nullable|string|max:500',
        'path_documents.*.title' => 'required|string|max:255',
        'path_documents.*.descripcion' => 'nullable|string|max:500',
        'imageFiles.*' => 'nullable|image|max:2048', // 2MB max
        'videoFiles.*' => 'nullable|mimes:mp4,avi,mov,wmv|max:10240', // 10MB max
        'documentFiles.*' => 'nullable|mimes:pdf,doc,docx|max:5120', // 5MB max
        // Unidades del proyecto
        'available_units' => 'nullable|integer|min:0',
        'reserved_units' => 'nullable|integer|min:0',
        'sold_units' => 'nullable|integer|min:0',
        'blocked_units' => 'nullable|integer|min:0',
        // Configuración general de precios del proyecto (promedios)
        'currency' => 'nullable|in:PEN,USD,EUR',
        'base_price' => 'nullable|numeric|min:0',
        'price_per_sqm' => 'nullable|numeric|min:0',
        'discount_percentage' => 'nullable|numeric|min:0|max:100',
        'final_price' => 'nullable|numeric|min:0',
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
        $this->path_image_portada = '';
        $this->path_video_portada = '';
        $this->imagePortadaFile = null;
        $this->videoPortadaFile = null;
        $this->path_images = [];
        $this->path_videos = [];
        $this->path_documents = [];
        $this->imageFiles = [];
        $this->videoFiles = [];
        $this->documentFiles = [];
        $this->selectedAdvisorId = '';
        $this->isPrimaryAdvisor = false;
        $this->advisorNotes = '';
        
        // Resetear unidades del proyecto
        $this->available_units = 0;
        $this->reserved_units = 0;
        $this->sold_units = 0;
        $this->blocked_units = 0;
        
        // Resetear configuración general de precios del proyecto
        $this->currency = 'PEN';
        $this->base_price = 0;
        $this->price_per_sqm = 0;
        $this->discount_percentage = 0;
        $this->final_price = 0;
        $this->accepts_credit = false;
        $this->accepts_cash = false;
        $this->accepts_transfer = false;
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
        $this->path_image_portada = $project->path_image_portada ?? '';
        $this->path_video_portada = $project->path_video_portada ?? '';
        
        // Unidades del proyecto
        $this->available_units = $project->available_units ?? 0;
        $this->reserved_units = $project->reserved_units ?? 0;
        $this->sold_units = $project->sold_units ?? 0;
        $this->blocked_units = $project->blocked_units ?? 0;
        
        // Configuración general de precios del proyecto (promedios)
        $this->currency = $project->currency ?? 'PEN';
        $this->base_price = $project->base_price ?? 0;
        $this->price_per_sqm = $project->price_per_sqm ?? 0;
        $this->discount_percentage = $project->discount_percentage ?? 0;
        $this->final_price = $project->final_price ?? 0;
        $this->accepts_credit = $project->accepts_credit ?? false;
        $this->accepts_cash = $project->accepts_cash ?? false;
        $this->accepts_transfer = $project->accepts_transfer ?? false;
        
        // Asegurar que los arrays multimedia tengan la estructura correcta
        $this->path_images = $this->ensureMultimediaArrayStructure($project->path_images ?? []);
        $this->path_videos = $this->ensureMultimediaArrayStructure($project->path_videos ?? []);
        $this->path_documents = $this->ensureMultimediaArrayStructure($project->path_documents ?? []);
        
        // Inicializar los arrays de archivos con el mismo tamaño que los arrays multimedia
        $this->imageFiles = array_fill(0, count($this->path_images), null);
        $this->videoFiles = array_fill(0, count($this->path_videos), null);
        $this->documentFiles = array_fill(0, count($this->path_documents), null);
    }

    /**
     * Asegura que los arrays multimedia tengan la estructura correcta
     */
    private function ensureMultimediaArrayStructure($array)
    {
        if (!is_array($array)) {
            return [];
        }
        
        $structuredArray = [];
        foreach ($array as $item) {
            if (is_array($item)) {
                $structuredArray[] = [
                    'title' => $item['title'] ?? '',
                    'path' => $item['path'] ?? '',
                    'descripcion' => $item['descripcion'] ?? '',
                    'type' => $item['type'] ?? ''
                ];
            } else {
                // Si es un string simple, crear estructura básica
                $structuredArray[] = [
                    'title' => '',
                    'path' => $item,
                    'descripcion' => '',
                    'type' => ''
                ];
            }
        }
        
        return $structuredArray;
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
            'path_image_portada' => $this->path_image_portada,
            'path_video_portada' => $this->path_video_portada,
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
            'path_image_portada' => $this->path_image_portada,
            'path_video_portada' => $this->path_video_portada,
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
    public function removeImagePortada()
    {
        $this->path_image_portada = '';
        $this->imagePortadaFile = null;
    }

    public function removeVideoPortada()
    {
        $this->path_video_portada = '';
        $this->videoPortadaFile = null;
    }

    public function addImage()
    {
        $this->path_images[] = [
            'title' => '',
            'path' => '',
            'descripcion' => '',
            'type' => ''
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
            'title' => '',
            'path' => '',
            'descripcion' => '',
            'type' => ''
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
            'title' => '',
            'path' => '',
            'descripcion' => '',
            'type' => ''
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

        // Procesar imágenes
        foreach ($this->imageFiles as $index => $file) {
            if ($file && isset($this->path_images[$index]) && $this->path_images[$index]['title']) {
                $path = $file->store('projects/images', 'public');
                $this->path_images[$index]['path'] = '/storage/' . $path;
            }
        }

        // Procesar videos
        foreach ($this->videoFiles as $index => $file) {
            if ($file && isset($this->path_videos[$index]) && $this->path_videos[$index]['title']) {
                $path = $file->store('projects/videos', 'public');
                $this->path_videos[$index]['path'] = '/storage/' . $path;
            }
        }

        // Procesar documentos
        foreach ($this->documentFiles as $index => $file) {
            if ($file && isset($this->path_documents[$index]) && $this->path_documents[$index]['title']) {
                $path = $file->store('projects/documents', 'public');
                $this->path_documents[$index]['path'] = '/storage/' . $path;
            }
        }
    }

    // Método para calcular el precio final promedio del proyecto
    public function updatedBasePrice()
    {
        $this->calculateFinalPrice();
    }

    public function updatedPricePerSqm()
    {
        $this->calculateFinalPrice();
    }

    public function updatedDiscountPercentage()
    {
        $this->calculateFinalPrice();
    }

    private function calculateFinalPrice()
    {
        $basePrice = floatval($this->base_price);
        $discount = floatval($this->discount_percentage);
        
        if ($basePrice > 0) {
            $discountAmount = ($basePrice * $discount) / 100;
            $this->final_price = $basePrice - $discountAmount;
        } else {
            $this->final_price = 0;
        }
    }

    // Método para validar que la suma de unidades sea correcta
    public function updatedTotalUnits()
    {
        $this->validateTotalUnits();
    }

    public function updatedAvailableUnits()
    {
        $this->validateTotalUnits();
    }

    public function updatedReservedUnits()
    {
        $this->validateTotalUnits();
    }

    public function updatedSoldUnits()
    {
        $this->validateTotalUnits();
    }

    public function updatedBlockedUnits()
    {
        $this->validateTotalUnits();
    }

    private function validateTotalUnits()
    {
        $total = intval($this->total_units);
        $available = intval($this->available_units);
        $reserved = intval($this->reserved_units);
        $sold = intval($this->sold_units);
        $blocked = intval($this->blocked_units);
        
        $calculatedTotal = $available + $reserved + $sold + $blocked;
        
        if ($calculatedTotal > $total) {
            $this->addError('total_units', 'La suma de todas las unidades no puede exceder el total del proyecto.');
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
    public function viewProject($projectId)
    {
        $this->selectedProject = $this->projectService->getProjectById($projectId);
        return redirect()->route('projects.project-view', $projectId);
    }
}
