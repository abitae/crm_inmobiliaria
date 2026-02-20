<?php

namespace App\Livewire\Settings;

use App\Models\City;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app')]
class CityList extends Component
{
    use Toast;
    use WithPagination;

    public $search = '';
    public $showCityModal = false;
    public $editingCity = null;
    public $name = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCityModal = true;
    }

    public function openEditModal(int $cityId): void
    {
        try {
            $city = City::find($cityId);
            if (!$city) {
                $this->error('Ciudad no encontrada.');
                return;
            }

            $this->editingCity = $city;
            $this->name = $city->name;
            $this->showCityModal = true;
        } catch (\Exception $e) {
            Log::error('Error al abrir modal de ciudad', [
                'city_id' => $cityId,
                'error' => $e->getMessage()
            ]);
            $this->error('Error al abrir el formulario: ' . $e->getMessage());
        }
    }

    public function closeCityModal(): void
    {
        $this->resetForm();
        $this->showCityModal = false;
    }

    public function saveCity(): void
    {
        $cityId = $this->editingCity?->id;
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities', 'name')->ignore($cityId),
            ],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'name.unique' => 'Esta ciudad ya existe.',
        ]);

        try {
            $payload = ['name' => trim($this->name)];

            if ($this->editingCity) {
                $this->editingCity->update($payload);
                $this->success('Ciudad actualizada correctamente.');
            } else {
                City::create($payload);
                $this->success('Ciudad creada correctamente.');
            }

            $this->closeCityModal();
        } catch (\Exception $e) {
            Log::error('Error al guardar ciudad', [
                'city_id' => $cityId,
                'error' => $e->getMessage()
            ]);
            $this->error('Error al guardar la ciudad: ' . $e->getMessage());
        }
    }

    public function deleteCity(int $cityId): void
    {
        try {
            $city = City::find($cityId);
            if (!$city) {
                $this->error('Ciudad no encontrada.');
                return;
            }

            $city->delete();
            $this->success('Ciudad eliminada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar ciudad', [
                'city_id' => $cityId,
                'error' => $e->getMessage()
            ]);
            $this->error('Error al eliminar la ciudad: ' . $e->getMessage());
        }
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'editingCity']);
    }

    public function render()
    {
        $query = City::query();
        if (trim($this->search) !== '') {
            $query->where('name', 'like', '%' . trim($this->search) . '%');
        }

        $cities = $query->orderBy('name')->paginate(10);

        return view('livewire.settings.city-list', [
            'cities' => $cities,
        ]);
    }
}
