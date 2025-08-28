<?php

namespace App\Livewire\Opportunities;

use App\Models\Opportunity;
use App\Services\OpportunityService;
use Livewire\Component;

class OpportunityDetail extends Component
{
    public $showModal = false;
    public $opportunityId = null;
    public $opportunity = null;

    protected $opportunityService;

    protected $listeners = [
        'open-opportunity-detail' => 'openModal'
    ];

    public function boot(OpportunityService $opportunityService)
    {
        $this->opportunityService = $opportunityService;
    }

    public function openModal($opportunityId)
    {
        $this->opportunityId = $opportunityId;
        $this->opportunity = $this->opportunityService->getOpportunityById($opportunityId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->opportunity = null;
        $this->opportunityId = null;
    }

    public function render()
    {
        return view('livewire.opportunities.opportunity-detail');
    }
}
