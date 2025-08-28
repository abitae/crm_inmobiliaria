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

class OpportunityServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected OpportunityService $opportunityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->opportunityService = new OpportunityService();
    }

    /** @test */
    public function it_can_get_all_opportunities_without_errors()
    {
        // Crear datos de prueba
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $unit = Unit::factory()->create();
        $advisor = User::factory()->create();

        // Crear oportunidades de prueba
        Opportunity::factory()->count(5)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'unit_id' => $unit->id,
            'advisor_id' => $advisor->id,
            'status' => 'activa',
            'stage' => 'contacto'
        ]);

        // Ejecutar la función
        $result = $this->opportunityService->getAllOpportunities(10);

        // Verificar que no hay errores
        $this->assertNotNull($result);
        $this->assertEquals(5, $result->total());
        $this->assertCount(5, $result->items());
    }

    /** @test */
    public function it_can_filter_opportunities_by_status()
    {
        // Crear datos de prueba
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        // Crear oportunidades con diferentes estados
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

        // Aplicar filtro
        $result = $this->opportunityService->getAllOpportunities(10, ['status' => 'activa']);

        // Verificar resultados
        $this->assertEquals(3, $result->total());
        $this->assertCount(3, $result->items());
    }

    /** @test */
    public function it_can_search_opportunities()
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
        $this->assertCount(1, $result->items());
    }

    /** @test */
    public function it_handles_empty_filters_correctly()
    {
        // Crear algunas oportunidades
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        Opportunity::factory()->count(3)->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
        ]);

        // Sin filtros
        $result = $this->opportunityService->getAllOpportunities(10, []);

        $this->assertEquals(3, $result->total());
        $this->assertCount(3, $result->items());
    }

    /** @test */
    public function it_can_handle_complex_filters()
    {
        // Crear datos de prueba
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $advisor = User::factory()->create();

        // Crear oportunidades con diferentes características
        Opportunity::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'status' => 'activa',
            'stage' => 'contacto',
            'probability' => 80,
            'expected_value' => 150000
        ]);

        Opportunity::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'advisor_id' => $advisor->id,
            'status' => 'activa',
            'stage' => 'propuesta',
            'probability' => 60,
            'expected_value' => 200000
        ]);

        // Aplicar filtros complejos
        $filters = [
            'status' => 'activa',
            'min_probability' => 70,
            'min_value' => 100000
        ];

        $result = $this->opportunityService->getAllOpportunities(10, $filters);

        // Solo debe devolver la oportunidad con probabilidad >= 70
        $this->assertEquals(1, $result->total());
        $this->assertCount(1, $result->items());
    }
}
