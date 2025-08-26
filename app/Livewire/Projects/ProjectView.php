<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class ProjectView extends Component
{
    use WithPagination;

    public $project;
    public $units;
    public $selectedUnit = null;
    public $showUnitDetails = false;
    
    // Búsqueda y filtros
    #[Url(as: 'search')]
    public $search = '';
    #[Url(as: 'status_filter')]
    public $statusFilter = '';
    #[Url(as: 'type_filter')]
    public $typeFilter = '';

    
    // Modal de medios
    public $showMediaModal = false;
    public $selectedMedia = null;
    public $mediaType = 'images'; // images, videos, documents
    public $currentMediaIndex = 0;

    protected $paginationTheme = 'tailwind';

    public function mount($projectId)
    {
        $this->project = Project::with([
            'units' => function ($query) {
                $query->with(['prices' => function ($q) {
                    $q->active()->valid();
                }]);
            },
            'prices' => function ($query) {
                $query->active()->valid();
            },
            'createdBy',
            'advisors'
        ])->findOrFail($projectId);
        
        $this->units = $this->project->units;
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

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->typeFilter = '';
        $this->resetPage();
    }

    public function selectUnit($unitId)
    {
        $this->selectedUnit = $this->units->find($unitId);
        $this->showUnitDetails = true;
    }

    public function closeUnitDetails()
    {
        $this->showUnitDetails = false;
        $this->selectedUnit = null;
    }

    public function openMediaModal($type = 'images', $index = 0)
    {
        $this->mediaType = $type;
        $this->currentMediaIndex = $index;
        $this->showMediaModal = true;
    }

    public function closeMediaModal()
    {
        $this->showMediaModal = false;
        $this->selectedMedia = null;
        $this->currentMediaIndex = 0;
    }

    public function nextMedia()
    {
        $mediaArray = $this->getMediaArray();
        if ($this->currentMediaIndex < count($mediaArray) - 1) {
            $this->currentMediaIndex++;
        }
    }

    public function previousMedia()
    {
        if ($this->currentMediaIndex > 0) {
            $this->currentMediaIndex--;
        }
    }

    public function selectMedia($index)
    {
        $this->currentMediaIndex = $index;
    }

    private function getMediaArray()
    {
        switch ($this->mediaType) {
            case 'images':
                // Las imágenes pueden ser strings simples o arrays con estructura
                if (is_array($this->project->path_images)) {
                    return array_map(function($img) {
                        if (is_array($img)) {
                            return [
                                'title' => $img['title'] ?? basename($img['path'] ?? ''),
                                'path' => $img['path'] ?? $img,
                                'descripcion' => $img['descripcion'] ?? '',
                                'type' => 'image'
                            ];
                        }
                        return [
                            'title' => basename($img),
                            'path' => $img,
                            'descripcion' => '',
                            'type' => 'image'
                        ];
                    }, $this->project->path_images);
                }
                return [];
            case 'videos':
                // Los videos pueden ser strings simples o arrays con estructura
                if (is_array($this->project->path_videos)) {
                    return array_map(function($video) {
                        if (is_array($video)) {
                            return [
                                'title' => $video['title'] ?? basename($video['path'] ?? ''),
                                'path' => $video['path'] ?? $video,
                                'descripcion' => $video['descripcion'] ?? '',
                                'type' => 'video'
                            ];
                        }
                        return [
                            'title' => basename($video),
                            'path' => $video,
                            'descripcion' => '',
                            'type' => 'video'
                        ];
                    }, $this->project->path_videos);
                }
                return [];
            case 'documents':
                // Los documentos tienen estructura: title, path, descripcion
                if (is_array($this->project->path_documents)) {
                    return array_map(function($doc) {
                        return [
                            'title' => $doc['title'] ?? 'Documento sin título',
                            'path' => $doc['path'] ?? '',
                            'descripcion' => $doc['descripcion'] ?? 'Sin descripción',
                            'type' => 'document'
                        ];
                    }, $this->project->path_documents);
                }
                return [];
            default:
                return [];
        }
    }

    public function getCurrentMediaProperty()
    {
        $mediaArray = $this->getMediaArray();
        if (isset($mediaArray[$this->currentMediaIndex])) {
            return $mediaArray[$this->currentMediaIndex];
        }
        return null;
    }

    public function getFilteredUnitsProperty()
    {
        $query = $this->project->units()->with(['prices' => function ($q) {
            $q->active()->valid();
        }]);

        // Filtro de búsqueda
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('unit_number', 'like', '%' . $this->search . '%')
                  ->orWhere('unit_type', 'like', '%' . $this->search . '%')
                  ->orWhere('tower', 'like', '%' . $this->search . '%')
                  ->orWhere('block', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro de estado
        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

                        // Filtro de tipo
                if (!empty($this->typeFilter)) {
                    $query->where('unit_type', $this->typeFilter);
                }

        return $query->orderBy('unit_number')->paginate(10);
    }

    public function render()
    {
        $filteredUnits = $this->filteredUnits;
        
        return view('livewire.projects.project-view', [
            'filteredUnits' => $filteredUnits,
            'statusOptions' => ['disponible', 'reservado', 'vendido', 'bloqueado'],
            'typeOptions' => ['casa', 'departamento', 'lote', 'oficina', 'local'],
        ]);
    }
}
