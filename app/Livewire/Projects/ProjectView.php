<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\Unit;
use App\Imports\UnitsImport;
use App\Exports\UnitsTemplateExport;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app')]
class ProjectView extends Component
{
    use WithPagination, WithFileUploads;

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
    #[Url(as: 'per_page')]
    public $perPage = 20;

    
    // Modal de medios
    public $showMediaModal = false;
    public $selectedMedia = null;
    public $mediaType = 'images'; // images, videos, documents
    public $currentMediaIndex = 0;
    
    // Modal específico para documentos PDF
    public $showPdfModal = false;
    public $selectedPdfDocument = null;
    public $currentPdfIndex = 0;
    
    // Modales para agregar medios
    public $showAddImagesModal = false;
    public $showAddVideosModal = false;
    public $showAddDocumentsModal = false;
    public $showAddUnitModal = false;
    public $showImportUnitsModal = false;
    
    // Propiedades para importar unidades
    public $importFile = null;
    public $importProgress = 0;
    public $importStatus = '';
    public $importErrors = [];
    public $importSuccessCount = 0;
    public $importErrorCount = 0;
    
    // Propiedades para agregar medios
    public $newImages = [];
    public $newVideos = [];
    public $newDocuments = [];
    public $imageTitles = [];
    public $videoTitles = [];
    public $documentTitles = [];
    public $imageDescriptions = [];
    public $videoDescriptions = [];
    public $documentDescriptions = [];

    // Propiedades para agregar unidades
    public $unit_number = '';
    public $unit_manzana = '';
    public $unit_type = '';
    public $tower = '';
    public $block = '';
    public $floor = '';
    public $area = '';
    public $bedrooms = '';
    public $bathrooms = '';
    public $parking_spaces = '';
    public $storage_rooms = '';
    public $balcony_area = '';
    public $terrace_area = '';
    public $garden_area = '';
    public $base_price = '';
    public $total_price = '';
    public $discount_percentage = '';
    public $commission_percentage = '';
    public $status = 'disponible';
    public $notes = '';

    // Propiedades para editar unidades
    public $editingUnit = null;
    public $isEditing = false;
    
    // Propiedades para selección múltiple de unidades
    public $selectedUnits = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $showDeleteMultipleModal = false;
    public $unitToDelete = null;

    // Reglas de validación para unidades (solo lotes)
    protected $unitRules = [
        'unit_number' => 'required|string|max:50',
        'unit_manzana' => 'nullable|string|max:50',
        'unit_type' => 'required|in:lote',
        'area' => 'required|numeric|min:0.01',
        'base_price' => 'required|numeric|min:0.01',
        'total_price' => 'required|numeric|min:0.01',
        'discount_percentage' => 'nullable|numeric|min:0|max:100',
        'commission_percentage' => 'nullable|numeric|min:0|max:100',
        'status' => 'required|in:disponible,reservado,vendido,transferido,cuotas',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $importRules = [
        'importFile' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max, solo Excel
    ];

    protected $unitMessages = [
        'unit_number.required' => 'El número de unidad es requerido',
        'unit_number.string' => 'El número de unidad debe ser una cadena de texto',
        'unit_number.max' => 'El número de unidad debe tener menos de 50 caracteres',
        'unit_number.unique' => 'El número de unidad ya existe en este proyecto',
        'unit_manzana.string' => 'La manzana debe ser una cadena de texto',
        'unit_manzana.max' => 'La manzana debe tener menos de 50 caracteres',
    ];

    protected $paginationTheme = 'tailwind';

    public function mount($projectId)
    {
        $this->project = Project::with([
            'units',
            'createdBy',
            'advisors'
        ])->findOrFail($projectId);
        
        $this->units = $this->project->units;
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->clearSelections();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
        $this->clearSelections();
    }
    
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
        $this->clearSelections();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->typeFilter = '';
        $this->resetPage();
        $this->clearSelections();
    }
    
    private function clearSelections()
    {
        $this->selectedUnits = [];
        $this->selectAll = false;
    }
    
    // ==================== MÉTODOS DE SELECCIÓN MÚLTIPLE ====================
    public function updatedSelectAll($value)
    {
        // Este método se ejecuta automáticamente cuando cambia el valor de selectAll
        // Obtener TODAS las unidades filtradas (no solo las de la página actual)
        $query = $this->project->units();
        
        // Aplicar los mismos filtros que se usan en filteredUnits
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('unit_number', 'like', '%' . $this->search . '%')
                  ->orWhere('unit_type', 'like', '%' . $this->search . '%')
                  ->orWhere('tower', 'like', '%' . $this->search . '%')
                  ->orWhere('block', 'like', '%' . $this->search . '%');
            });
        }
        
        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }
        
        if (!empty($this->typeFilter)) {
            $query->where('unit_type', $this->typeFilter);
        }
        
        // Obtener todos los IDs de las unidades filtradas
        $allFilteredIds = $query->pluck('id')->toArray();
        
        if ($value) {
            // Seleccionar TODAS las unidades filtradas
            // Combinar con las ya seleccionadas (de otras páginas/filtros anteriores)
            $this->selectedUnits = array_unique(array_merge($this->selectedUnits, $allFilteredIds));
        } else {
            // Deseleccionar TODAS las unidades filtradas
            $this->selectedUnits = array_values(array_diff($this->selectedUnits, $allFilteredIds));
        }
        
        // Asegurar que los valores estén ordenados y sean únicos
        $this->selectedUnits = array_values(array_unique($this->selectedUnits));
        
        // No llamar a updateSelectAllState aquí para evitar conflicto
        // Se actualizará automáticamente cuando se renderice la vista
    }
    
    public function toggleSelectAll()
    {
        // Método alternativo si se necesita llamar manualmente
        $this->updatedSelectAll($this->selectAll);
    }
    
    public function toggleUnitSelection($unitId)
    {
        if (in_array($unitId, $this->selectedUnits)) {
            // Deseleccionar
            $this->selectedUnits = array_values(array_diff($this->selectedUnits, [$unitId]));
        } else {
            // Seleccionar
            $this->selectedUnits[] = $unitId;
        }
        
        // Actualizar el estado de "seleccionar todo"
        $this->updateSelectAllState();
    }
    
    private function updateSelectAllState()
    {
        // Actualizar el estado de "seleccionar todo" basado en la página actual
        $filteredUnits = $this->filteredUnits;
        $currentPageIds = $filteredUnits->pluck('id')->toArray();
        $selectedInPage = array_intersect($this->selectedUnits, $currentPageIds);
        $this->selectAll = count($selectedInPage) === count($currentPageIds) && count($currentPageIds) > 0;
    }
    
    public function updatedSelectedUnits()
    {
        // Actualizar el estado de "seleccionar todo" cuando cambian las selecciones
        $this->updateSelectAllState();
    }
    
    // ==================== MÉTODOS DE ELIMINACIÓN ====================
    public function confirmDeleteUnit($unitId)
    {
        $this->unitToDelete = Unit::find($unitId);
        if ($this->unitToDelete) {
            $this->showDeleteModal = true;
        }
    }
    
    public function cancelDeleteUnit()
    {
        $this->showDeleteModal = false;
        $this->unitToDelete = null;
    }
    
    public function deleteUnit()
    {
        if (!$this->unitToDelete) {
            $this->showDeleteModal = false;
            return;
        }
        
        try {
            $unitNumber = $this->unitToDelete->unit_number;
            $unitId = $this->unitToDelete->id;
            $this->unitToDelete->delete();
            
            // Actualizar contadores del proyecto
            $this->project->updateUnitCounts();
            
            // Recargar las unidades
            $this->units = $this->project->fresh()->units;
            
            // Limpiar selección si estaba seleccionada
            $this->selectedUnits = array_values(array_diff($this->selectedUnits, [$unitId]));
            
            // Actualizar estado de selección
            $this->updateSelectAllState();
            
            $this->showDeleteModal = false;
            $this->unitToDelete = null;
            
            $this->dispatch('show-success', message: "Unidad {$unitNumber} eliminada exitosamente");
            
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Error al eliminar la unidad: ' . $e->getMessage());
            $this->showDeleteModal = false;
            $this->unitToDelete = null;
        }
    }
    
    public function confirmDeleteMultipleUnits()
    {
        if (empty($this->selectedUnits)) {
            $this->dispatch('show-error', message: 'No hay unidades seleccionadas');
            return;
        }
        
        $this->showDeleteMultipleModal = true;
    }
    
    public function cancelDeleteMultipleUnits()
    {
        $this->showDeleteMultipleModal = false;
    }
    
    public function deleteMultipleUnits()
    {
        if (empty($this->selectedUnits)) {
            $this->showDeleteMultipleModal = false;
            return;
        }
        
        try {
            $count = count($this->selectedUnits);
            $unitIds = $this->selectedUnits;
            
            // Eliminar las unidades
            Unit::whereIn('id', $unitIds)->delete();
            
            // Actualizar contadores del proyecto
            $this->project->updateUnitCounts();
            
            // Recargar las unidades
            $this->units = $this->project->fresh()->units;
            
            // Limpiar selección
            $this->clearSelections();
            
            $this->showDeleteMultipleModal = false;
            
            $this->dispatch('show-success', message: "{$count} unidad(es) eliminada(s) exitosamente");
            
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Error al eliminar las unidades: ' . $e->getMessage());
            $this->showDeleteMultipleModal = false;
        }
    }

    public function checkUnitNumberAvailability()
    {
        if (empty($this->unit_number)) {
            return;
        }

        // Validar la combinación de manzana y número de unidad
        $query = $this->project->units()->where('unit_number', $this->unit_number);
        
        // Filtrar por manzana (puede ser null o vacío)
        $manzana = $this->unit_manzana ? trim($this->unit_manzana) : null;
        if ($manzana) {
            $query->where('unit_manzana', $manzana);
        } else {
            $query->whereNull('unit_manzana');
        }
        
        if ($this->isEditing && $this->editingUnit) {
            $query->where('id', '!=', $this->editingUnit->id);
        }

        $exists = $query->exists();

        if ($exists) {
            $manzanaText = $manzana ? "Manzana {$manzana}" : "Sin manzana";
            $this->dispatch('show-error', message: "La combinación {$manzanaText} - Unidad {$this->unit_number} ya existe en este proyecto");
        }
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
    
    // Métodos para el modal de PDFs
    public function openPdfModal($index = 0)
    {
        $this->currentPdfIndex = $index;
        $this->showPdfModal = true;
        
        // Dispatch event para mostrar indicador de carga
        $this->dispatch('openPdfModal');
    }
    
    public function closePdfModal()
    {
        $this->showPdfModal = false;
        $this->selectedPdfDocument = null;
        $this->currentPdfIndex = 0;
    }
    
    public function nextPdf()
    {
        $pdfArray = $this->getPdfDocuments();
        if ($this->currentPdfIndex < count($pdfArray) - 1) {
            $this->currentPdfIndex++;
        }
    }
    
    public function previousPdf()
    {
        if ($this->currentPdfIndex > 0) {
            $this->currentPdfIndex--;
        }
    }

    public function selectPdf($index)
    {
        $this->currentPdfIndex = $index;
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
    
    // Métodos para agregar medios
    public function addImages()
    {
        $this->showAddImagesModal = true;
        $this->newImages = [];
        $this->imageTitles = [];
        $this->imageDescriptions = [];
    }
    
    public function addVideos()
    {
        $this->showAddVideosModal = true;
        $this->newVideos = [];
        $this->videoTitles = [];
        $this->videoDescriptions = [];
    }
    
    public function closeAddImagesModal()
    {
        $this->showAddImagesModal = false;
        $this->newImages = [];
        $this->imageTitles = [];
        $this->imageDescriptions = [];
    }
    
    public function closeAddVideosModal()
    {
        $this->showAddVideosModal = false;
        $this->newVideos = [];
        $this->videoTitles = [];
        $this->videoDescriptions = [];
    }
    
    public function updatedNewImages()
    {
        // Limpiar arrays de títulos y descripciones cuando se cambian las imágenes
        $this->imageTitles = array_fill(0, count($this->newImages), '');
        $this->imageDescriptions = array_fill(0, count($this->newImages), '');
    }
    
    public function updatedNewVideos()
    {
        // Limpiar arrays de títulos y descripciones cuando se cambian los videos
        $this->videoTitles = array_fill(0, count($this->newVideos), '');
        $this->videoDescriptions = array_fill(0, count($this->newVideos), '');
    }
    
    public function saveImages()
    {
        $this->validate([
            'newImages.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'imageTitles.*' => 'nullable|string|max:255',
            'imageDescriptions.*' => 'nullable|string|max:1000',
        ]);
        
        try {
            $uploadedImages = [];
            
            foreach ($this->newImages as $index => $image) {
                $path = $image->store('projects/' . $this->project->id . '/images', 'public');
                
                $uploadedImages[] = [
                    'title' => $this->imageTitles[$index] ?: basename($image->getClientOriginalName()),
                    'path' => $path,
                    'descripcion' => $this->imageDescriptions[$index] ?: '',
                    'type' => 'image'
                ];
            }
            
            // Obtener imágenes existentes
            $existingImages = $this->project->path_images ?: [];
            
            // Agregar nuevas imágenes
            $allImages = array_merge($existingImages, $uploadedImages);
            
            // Actualizar el proyecto
            $this->project->update([
                'path_images' => $allImages
            ]);
            
            // Actualizar contadores de unidades
            $this->project->updateUnitCounts();
            
            $this->closeAddImagesModal();
            
            // Mostrar mensaje de éxito
            $this->dispatch('show-success', message: 'Imágenes agregadas exitosamente');
            
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Error al guardar las imágenes: ' . $e->getMessage());
        }
    }
    
    public function saveVideos()
    {
        $this->validate([
            'newVideos.*' => 'required|mimes:mp4,avi,mov,wmv,flv,webm|max:102400', // 100MB max
            'videoTitles.*' => 'nullable|string|max:255',
            'videoDescriptions.*' => 'nullable|string|max:1000',
        ]);
        
        try {
            $uploadedVideos = [];
            
            foreach ($this->newVideos as $index => $video) {
                $path = $video->store('projects/' . $this->project->id . '/videos', 'public');
                
                $uploadedVideos[] = [
                    'title' => $this->videoTitles[$index] ?: basename($video->getClientOriginalName()),
                    'path' => $path,
                    'descripcion' => $this->videoDescriptions[$index] ?: '',
                    'type' => 'video'
                ];
            }
            
            // Obtener videos existentes
            $existingVideos = $this->project->path_videos ?: [];
            
            // Agregar nuevos videos
            $allVideos = array_merge($existingVideos, $uploadedVideos);
            
            // Actualizar el proyecto
            $this->project->update([
                'path_videos' => $allVideos
            ]);
            
            // Actualizar contadores de unidades
            $this->project->updateUnitCounts();
            
            $this->closeAddVideosModal();
            
            // Mostrar mensaje de éxito
            $this->dispatch('show-success', message: 'Videos agregados exitosamente');
            
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Error al guardar los videos: ' . $e->getMessage());
        }
    }
    
    public function addDocuments()
    {
        $this->showAddDocumentsModal = true;
        $this->newDocuments = [];
        $this->documentTitles = [];
        $this->documentDescriptions = [];
    }

    public function closeAddDocumentsModal()
    {
        $this->showAddDocumentsModal = false;
        $this->newDocuments = [];
        $this->documentTitles = [];
        $this->documentDescriptions = [];
    }

    public function updatedNewDocuments()
    {
        // Limpiar arrays de títulos y descripciones cuando se cambian los documentos
        $this->documentTitles = array_fill(0, count($this->newDocuments), '');
        $this->documentDescriptions = array_fill(0, count($this->newDocuments), '');
    }

    public function saveDocuments()
    {
        $this->validate([
            'newDocuments.*' => 'required|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf|max:51200', // 50MB max
            'documentTitles.*' => 'nullable|string|max:255',
            'documentDescriptions.*' => 'nullable|string|max:1000',
        ]);
        
        try {
            $uploadedDocuments = [];
            
            foreach ($this->newDocuments as $index => $document) {
                $path = $document->store('projects/' . $this->project->id . '/documents', 'public');
                
                $uploadedDocuments[] = [
                    'title' => $this->documentTitles[$index] ?: basename($document->getClientOriginalName()),
                    'path' => $path,
                    'descripcion' => $this->documentDescriptions[$index] ?: '',
                    'type' => 'document'
                ];
            }
            
            // Obtener documentos existentes
            $existingDocuments = $this->project->path_documents ?: [];
            
            // Agregar nuevos documentos
            $allDocuments = array_merge($existingDocuments, $uploadedDocuments);
            
            // Actualizar el proyecto
            $this->project->update([
                'path_documents' => $allDocuments
            ]);
            
            // Actualizar contadores de unidades
            $this->project->updateUnitCounts();
            
            $this->closeAddDocumentsModal();
            
            // Mostrar mensaje de éxito
            $this->dispatch('show-success', message: 'Documentos agregados exitosamente');
            
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Error al guardar los documentos: ' . $e->getMessage());
        }
    }
    
    
    
    

    public function deleteMedia($index)
    {
        try {
            
            // Lógica original para otros tipos de medios
            $mediaArray = $this->getMediaArray();
            
            if (!isset($mediaArray[$index])) {
                $this->dispatch('show-error', message: 'Archivo no encontrado');
                return;
            }

            $mediaToDelete = $mediaArray[$index];
            $filePath = $mediaToDelete['path'];
            
            // Eliminar archivo físico del storage
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            
            // Eliminar del array correspondiente según el tipo
            if ($this->mediaType === 'images') {
                $existingImages = $this->project->path_images ?: [];
                unset($existingImages[$index]);
                $this->project->update([
                    'path_images' => array_values($existingImages)
                ]);
            } elseif ($this->mediaType === 'videos') {
                $existingVideos = $this->project->path_videos ?: [];
                unset($existingVideos[$index]);
                $this->project->update([
                    'path_videos' => array_values($existingVideos)
                ]);
            } elseif ($this->mediaType === 'documents') {
                $existingDocuments = $this->project->path_documents ?: [];
                unset($existingDocuments[$index]);
                $this->project->update([
                    'path_documents' => array_values($existingDocuments)
                ]);
            }
            
            // Actualizar contadores de unidades
            $this->project->updateUnitCounts();
            
            // Ajustar el índice actual si es necesario
            $newMediaArray = $this->getMediaArray();
            if (count($newMediaArray) === 0) {
                // Si no quedan medios, cerrar el modal
                $this->closeMediaModal();
            } else {
                // Ajustar el índice actual
                if ($this->currentMediaIndex >= count($newMediaArray)) {
                    $this->currentMediaIndex = count($newMediaArray) - 1;
                }
            }
            
            $this->dispatch('show-success', message: 'Archivo eliminado exitosamente');
            
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Error al eliminar el archivo: ' . $e->getMessage());
        }
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
    
    private function getPdfDocuments()
    {
        // Cache para evitar procesamiento repetido
        static $pdfCache = null;
        static $lastProjectId = null;
        
        if ($pdfCache !== null && $lastProjectId === $this->project->id) {
            return $pdfCache;
        }
        
        $pdfDocuments = [];
        
        if (is_array($this->project->path_documents)) {
            foreach ($this->project->path_documents as $doc) {
                $path = is_array($doc) ? $doc['path'] : $doc;
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                
                if ($extension === 'pdf') {
                    $pdfDocuments[] = [
                        'title' => is_array($doc) ? ($doc['title'] ?? basename($path)) : basename($path),
                        'path' => $path,
                        'descripcion' => is_array($doc) ? ($doc['descripcion'] ?? 'Sin descripción') : 'Sin descripción',
                        'type' => 'pdf',
                        'size' => $this->getFileSize($path),
                        'modified' => $this->getFileModifiedTime($path)
                    ];
                }
            }
        }
        
        // Ordenar por título para mejor UX
        usort($pdfDocuments, function($a, $b) {
            return strcasecmp($a['title'], $b['title']);
        });
        
        $pdfCache = $pdfDocuments;
        $lastProjectId = $this->project->id;
        
        return $pdfDocuments;
    }
    
    public function getCurrentPdfProperty()
    {
        $pdfArray = $this->getPdfDocuments();
        if (isset($pdfArray[$this->currentPdfIndex])) {
            return $pdfArray[$this->currentPdfIndex];
        }
        return null;
    }
    
    public function getPdfDocumentsCountProperty()
    {
        return count($this->getPdfDocuments());
    }
    
    public function getPdfDocumentsProperty()
    {
        return $this->getPdfDocuments();
    }
    
    public function downloadPdf($index)
    {
        $pdfArray = $this->getPdfDocuments();
        
        if (!isset($pdfArray[$index])) {
            $this->dispatch('show-error', message: 'Documento no encontrado');
            return;
        }
        
        $pdf = $pdfArray[$index];
        $filePath = storage_path('app/public/' . $pdf['path']);
        
        if (!file_exists($filePath)) {
            $this->dispatch('show-error', message: 'El archivo no existe en el servidor');
            return;
        }
        
        return response()->download($filePath, $pdf['title'] . '.pdf');
    }
    
    public function downloadDocument($index)
    {
        $mediaArray = $this->getMediaArray();
        
        if (!isset($mediaArray[$index])) {
            $this->dispatch('show-error', message: 'Documento no encontrado');
            return;
        }
        
        $document = $mediaArray[$index];
        $filePath = storage_path('app/public/' . $document['path']);
        
        if (!file_exists($filePath)) {
            $this->dispatch('show-error', message: 'El archivo no existe en el servidor');
            return;
        }
        
        $extension = pathinfo($document['path'], PATHINFO_EXTENSION);
        $fileName = $document['title'] . '.' . $extension;
        
        return response()->download($filePath, $fileName);
    }
    
    private function getFileSize($path)
    {
        try {
            $fullPath = storage_path('app/public/' . $path);
            if (file_exists($fullPath)) {
                $bytes = filesize($fullPath);
                return $this->formatFileSize($bytes);
            }
        } catch (\Exception $e) {
            // Ignorar errores de archivo
        }
        return 'N/A';
    }
    
    private function getFileModifiedTime($path)
    {
        try {
            $fullPath = storage_path('app/public/' . $path);
            if (file_exists($fullPath)) {
                return date('d/m/Y H:i', filemtime($fullPath));
            }
        } catch (\Exception $e) {
            // Ignorar errores de archivo
        }
        return 'N/A';
    }
    
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
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
        $query = $this->project->units();

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

        $perPage = $this->perPage === 'all' ? 999999 : (int)$this->perPage;
        return $query->orderBy('unit_manzana')->orderBy('unit_number')->paginate($perPage);
    }
    public function addUnit()
    {
        $this->resetUnitForm();
        $this->showAddUnitModal = true;
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->editingUnit = null;
        $this->resetUnitForm();
    }

    public function closeAddUnitModal()
    {
        $this->showAddUnitModal = false;
        $this->isEditing = false;
        $this->editingUnit = null;
        $this->resetUnitForm();
    }

    public function resetUnitForm()
    {
        $this->reset([
            'unit_number',
            'unit_manzana',
            'unit_type',
            'tower',
            'block',
            'floor',
            'area',
            'bedrooms',
            'bathrooms',
            'parking_spaces',
            'storage_rooms',
            'balcony_area',
            'terrace_area',
            'garden_area',
            'base_price',
            'total_price',
            'discount_percentage',
            'commission_percentage',
            'status',
            'notes'
        ]);
        $this->status = 'disponible';
        $this->unit_type = 'lote'; // Establecer tipo lote por defecto
        $this->isEditing = false;
        $this->editingUnit = null;
    }

    public function saveUnit()
    {
        // Aplicar reglas de validación dinámicas
        $rules = $this->unitRules;
        
        // Validación personalizada de unicidad basada en la combinación de unit_manzana y unit_number
        $manzana = $this->unit_manzana ? trim($this->unit_manzana) : null;
        
        $rules['unit_number'] = [
            'required',
            'string',
            'max:50',
            function ($attribute, $value, $fail) use ($manzana) {
                $query = $this->project->units()
                    ->where('unit_number', $value);
                
                // Filtrar por manzana
                if ($manzana) {
                    $query->where('unit_manzana', $manzana);
                } else {
                    $query->whereNull('unit_manzana');
                }
                
                // Si estamos editando, excluir la unidad actual
                if ($this->isEditing && $this->editingUnit) {
                    $query->where('id', '!=', $this->editingUnit->id);
                }
                
                if ($query->exists()) {
                    $manzanaText = $manzana ? "Manzana {$manzana}" : "Sin manzana";
                    $fail("La combinación {$manzanaText} - Unidad {$value} ya existe en este proyecto.");
                }
            }
        ];
        
        $this->validate($rules, $this->unitMessages);
        

        try {
            // Calcular precio final
            $discountAmount = 0;
            if ($this->discount_percentage > 0) {
                $discountAmount = ($this->total_price * $this->discount_percentage) / 100;
            }
            $finalPrice = $this->total_price - $discountAmount;

            // Calcular comisión
            $commissionAmount = 0;
            if ($this->commission_percentage > 0) {
                $commissionAmount = ($finalPrice * $this->commission_percentage) / 100;
            }

            // Preparar datos para guardar (solo lotes)
            $unitData = [
                'unit_number' => $this->unit_number,
                'unit_manzana' => $this->unit_manzana,
                'unit_type' => 'lote', // Siempre lote
                'tower' => null,
                'block' => null,
                'floor' => null,
                'area' => $this->area,
                'bedrooms' => 0,
                'bathrooms' => 0,
                'parking_spaces' => 0,
                'storage_rooms' => 0,
                'balcony_area' => 0,
                'terrace_area' => 0,
                'garden_area' => 0,
                'base_price' => $this->base_price,
                'total_price' => $this->total_price,
                'discount_percentage' => $this->discount_percentage ?: 0,
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice,
                'commission_percentage' => $this->commission_percentage ?: 0,
                'commission_amount' => $commissionAmount,
                'status' => $this->status,
                'notes' => $this->notes,
                'updated_by' => 1,
            ];

            if ($this->isEditing && $this->editingUnit) {
                // Actualizar unidad existente
                $this->editingUnit->update($unitData);
                $unit = $this->editingUnit;
                $message = 'Unidad actualizada exitosamente';
            } else {
                // Crear nueva unidad
                $unitData['created_by'] = 1;
                $unit = $this->project->units()->create($unitData);
                $message = 'Unidad agregada exitosamente';
            }

            // Actualizar contadores del proyecto
            $this->project->updateUnitCounts();
            
            // Recargar las unidades
            $this->units = $this->project->fresh()->units;

            $this->closeAddUnitModal();
            $this->dispatch('show-success', message: $message);
            
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Error al crear la unidad: ' . $e->getMessage());
        }
    }
    public function editUnit($unitId)
    {
        $this->showAddUnitModal = true;
        $this->editingUnit = Unit::find($unitId);
        $this->isEditing = true;
        $this->unit_number = $this->editingUnit->unit_number;
        $this->unit_manzana = $this->editingUnit->unit_manzana;
        $this->unit_type = 'lote'; // Siempre lote
        $this->area = $this->editingUnit->area;
        $this->base_price = $this->editingUnit->base_price;
        $this->total_price = $this->editingUnit->total_price;
        $this->discount_percentage = $this->editingUnit->discount_percentage ?? 0;
        $this->commission_percentage = $this->editingUnit->commission_percentage ?? 0;
        $this->status = $this->editingUnit->status;
        $this->notes = $this->editingUnit->notes;
    }
    public function importUnits()
    {
        $this->resetImportData();
        $this->showImportUnitsModal = true;
    }

    public function closeImportUnitsModal()
    {
        $this->showImportUnitsModal = false;
        $this->resetImportData();
    }

    public function resetImportData()
    {
        $this->importFile = null;
        $this->importProgress = 0;
        $this->importStatus = '';
        $this->importErrors = [];
        $this->importSuccessCount = 0;
        $this->importErrorCount = 0;
    }

    public function downloadTemplate()
    {
        $filename = 'plantilla_unidades_' . str_replace(' ', '_', $this->project->name) . '.xlsx';
        
        $headers = [
            'numero_unidad',
            'manzana',
            'tipo',
            'torre',
            'bloque',
            'piso',
            'area',
            'dormitorios',
            'banos',
            'estacionamientos',
            'cocheras',
            'area_balcon',
            'area_terraza',
            'area_jardin',
            'precio_base',
            'precio_total',
            'descuento_porcentaje',
            'comision_porcentaje',
            'estado',
            'notas'
        ];
        
        $sampleData = [
            [
                'L-001',
                'Manzana A',
                'lote',
                '',
                '',
                '',
                200.00,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                15000,
                3000000,
                5,
                3,
                'disponible',
                'Lote con buena ubicación'
            ]
        ];
        
        return Excel::download(new UnitsTemplateExport($headers, $sampleData), $filename);
    }

    public function processImport()
    {
        $this->validate($this->importRules);
        
        $this->importStatus = 'Procesando archivo...';
        $this->importProgress = 10;
        
        try {
            // Obtener la ruta real del archivo temporal de Livewire
            $fullPath = $this->importFile->getRealPath();
            
            // Verificar que el archivo existe
            if (!file_exists($fullPath)) {
                throw new \Exception('No se pudo acceder al archivo. Por favor, intente subir el archivo nuevamente.');
            }
            
            $this->importProgress = 30;
            $this->importStatus = 'Leyendo datos del archivo...';
            
            // Crear instancia de la clase de importación
            $import = new UnitsImport($this->project);
            
            $this->importProgress = 50;
            $this->importStatus = 'Validando y procesando datos...';
            
            // Importar usando maatwebsite/excel directamente desde la ruta temporal
            Excel::import($import, $fullPath);
            
            // Obtener estadísticas
            $this->importSuccessCount = $import->getSuccessCount();
            $this->importErrorCount = $import->getErrorCount();
            $this->importErrors = $import->getErrors();
            
            // Agregar errores de validación si existen (del trait SkipsFailures)
            if ($import->failures()->count() > 0) {
                foreach ($import->failures() as $failure) {
                    $row = $failure->row();
                    $errors = $failure->errors();
                    $this->importErrors[] = "Fila {$row}: " . implode(', ', $errors);
                }
                $this->importErrorCount += $import->failures()->count();
            }
            
            // Actualizar contadores del proyecto
            $this->project->updateUnitCounts();
            
            // Recargar las unidades
            $this->units = $this->project->fresh()->units;
            
            $this->importProgress = 100;
            $this->importStatus = "Importación completada. {$this->importSuccessCount} unidades importadas exitosamente.";
            
            if ($this->importErrorCount > 0) {
                $this->importStatus .= " {$this->importErrorCount} error(es) encontrado(s).";
            }
            
        } catch (\Exception $e) {
            $this->importStatus = 'Error durante la importación: ' . $e->getMessage();
            $this->importErrorCount++;
            $this->importErrors[] = $e->getMessage();
        }
    }

    public function render()
    {
        $filteredUnits = $this->filteredUnits;
        
        return view('livewire.projects.project-view', [
            'filteredUnits' => $filteredUnits,
            'statusOptions' => ['disponible', 'reservado', 'vendido', 'transferido', 'cuotas'],
            'typeOptions' => ['lote'], // Solo lotes
        ]);
    }
}
