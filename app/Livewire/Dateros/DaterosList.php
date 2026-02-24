<?php

namespace App\Livewire\Dateros;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('components.layouts.app')]
class DaterosList extends Component
{
    use Toast;
    use WithPagination;

    // Filtros
    public $search = '';
    public $statusFilter = 'all'; // 'all', 'active', 'inactive'
    public $vendedorFilter = '';

    /**
     * Query base de dateros (solo líderes ven sus dateros).
     */
    private function baseQuery()
    {
        $user = Auth::user();
        return User::where('lider_id', $user->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'datero'));
    }


    /**
     * Resetea la paginación cuando cambian los filtros
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedVendedorFilter()
    {
        $this->resetPage();
    }

    /**
     * Limpia los filtros
     */
    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'vendedorFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->baseQuery();

        if ($this->search !== '') {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            });
        }
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }
        if ($this->vendedorFilter !== '') {
            $query->where('id', $this->vendedorFilter);
        }

        $vendedores = $query->with('lider.roles')->orderBy('name')->paginate(15);

        $daterosForSelect = $this->baseQuery()->orderBy('name')->get(['id', 'name']);

        return view('livewire.dateros.dateros-list', [
            'vendedores' => $vendedores,
            'daterosForSelect' => $daterosForSelect,
        ]);
    }
}
