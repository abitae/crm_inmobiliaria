<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\OpportunityService;
use App\Models\Opportunity;
use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class OpportunityFiltersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected OpportunityService $opportunityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->opportunityService = new OpportunityService();
    }

    /** @test */
    public function it_can_filter_by_search_term()
    {
        // Crear datos de prueba
        $client = Client::factory()->create(['name' => 'Cliente Test']);
        $project = Project::factory()->create(['name' => 'Proyecto Test']);
        $advisor = User::factory()->create();

        Opportunity::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
        ]);

        // Buscar por nombre del cliente
        $result = $this->opportunityService->getAllOpportunities(10, ['search' => 'Cliente']);
        $this->assertEquals(1, $result->total());

        // Buscar por nombre del proyecto
        $result = $this->opportunityService->getAllOpportunities(10, ['search' => 'Proyecto']);
        $this->assertEquals(1, $result->total());
    }

    /** @test */
    public function it_can_filter_by_status()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'status' => 'activa'
        ]);

        Opportunity::factory()->count(2)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'status' => 'ganada'
        ]);

        $result = $this->opportunityService->getAllOpportunities(10, ['status' => 'activa']);
        $this->assertEquals(3, $result->total());
    }

    /** @test */
    public function it_can_filter_by_stage()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(2)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'stage' => 'contacto'
        ]);

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'stage' => 'propuesta'
        ]);

        $result = $this->opportunityService->getAllOpportunities(10, ['stage' => 'contacto']);
        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_can_filter_by_source()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(2)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'source' => 'website'
        ]);

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'source' => 'referral'
        ]);

        $result = $this->opportunityService->getAllOpportunities(10, ['source' => 'website']);
        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_can_filter_by_campaign()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(2)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'campaign' => 'navidad2024'
        ]);

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'campaign' => 'verano2024'
        ]);

        $result = $this->opportunityService->getAllOpportunities(10, ['campaign' => 'navidad2024']);
        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_can_filter_by_probability_range()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(2)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'probability' => 80
        ]);

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'probability' => 60
        ]);

        $result = $this->opportunityService->getAllOpportunities(10, [
            'min_probability' => 70,
            'max_probability' => 90
        ]);
        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_can_filter_by_value_range()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(2)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'expected_value' => 150000
        ]);

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'expected_value' => 80000
        ]);

        $result = $this->opportunityService->getAllOpportunities(10, [
            'min_value' => 100000,
            'max_value' => 200000
        ]);
        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_can_filter_by_date_range()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(2)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'expected_close_date' => now()->addDays(30)
        ]);

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'expected_close_date' => now()->addDays(90)
        ]);

        $result = $this->opportunityService->getAllOpportunities(10, [
            'date_from' => now()->addDays(20),
            'date_to' => now()->addDays(40)
        ]);
        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_can_filter_overdue_opportunities()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(2)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'expected_close_date' => now()->subDays(10),
            'status' => 'activa'
        ]);

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'expected_close_date' => now()->addDays(30),
            'status' => 'activa'
        ]);

        $result = $this->opportunityService->getAllOpportunities(10, ['overdue' => true]);
        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_can_filter_closing_this_month()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(2)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'expected_close_date' => now()->endOfMonth(),
            'status' => 'activa'
        ]);

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'expected_close_date' => now()->addMonths(2),
            'status' => 'activa'
        ]);

        $result = $this->opportunityService->getAllOpportunities(10, ['closing_this_month' => true]);
        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_can_filter_high_probability_opportunities()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(2)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'probability' => 85
        ]);

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'probability' => 60
        ]);

        $result = $this->opportunityService->getAllOpportunities(10, ['high_probability' => true]);
        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_can_combine_multiple_filters()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'status' => 'activa',
            'stage' => 'propuesta',
            'probability' => 80,
            'expected_value' => 150000,
            'source' => 'website'
        ]);

        Opportunity::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'status' => 'activa',
            'stage' => 'contacto',
            'probability' => 60,
            'expected_value' => 80000,
            'source' => 'referral'
        ]);

        $filters = [
            'status' => 'activa',
            'min_probability' => 70,
            'min_value' => 100000,
            'source' => 'website'
        ];

        $result = $this->opportunityService->getAllOpportunities(10, $filters);
        $this->assertEquals(1, $result->total());
    }

    /** @test */
    public function it_handles_empty_filters_correctly()
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
        ]);

        $result = $this->opportunityService->getAllOpportunities(10, []);
        $this->assertEquals(3, $result->total());
    }
}
