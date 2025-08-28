<?php

namespace Database\Factories;

use App\Models\Opportunity;
use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Opportunity>
 */
class OpportunityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Opportunity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stages = ['captado', 'calificado', 'contacto', 'propuesta', 'visita', 'negociacion', 'cierre'];
        $statuses = ['activa', 'ganada', 'perdida', 'cancelada'];
        $sources = ['website', 'referral', 'cold_call', 'social_media', 'event', 'other'];
        
        return [
            'client_id' => Client::factory(),
            'project_id' => Project::factory(),
            'unit_id' => Unit::factory(),
            'advisor_id' => User::factory(),
            'stage' => $this->faker->randomElement($stages),
            'status' => $this->faker->randomElement($statuses),
            'probability' => $this->faker->numberBetween(10, 100),
            'expected_value' => $this->faker->randomFloat(2, 50000, 1000000),
            'expected_close_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'actual_close_date' => null,
            'close_value' => null,
            'close_reason' => null,
            'lost_reason' => null,
            'notes' => $this->faker->optional()->paragraph(),
            'source' => $this->faker->randomElement($sources),
            'campaign' => $this->faker->optional()->word(),
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    /**
     * Indicate that the opportunity is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'activa',
        ]);
    }

    /**
     * Indicate that the opportunity is won.
     */
    public function won(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ganada',
            'actual_close_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'close_value' => $this->faker->randomFloat(2, 50000, 1000000),
            'probability' => 100,
        ]);
    }

    /**
     * Indicate that the opportunity is lost.
     */
    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'perdida',
            'actual_close_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'lost_reason' => $this->faker->sentence(),
            'probability' => 0,
        ]);
    }

    /**
     * Indicate that the opportunity is in a specific stage.
     */
    public function stage(string $stage): static
    {
        return $this->state(fn (array $attributes) => [
            'stage' => $stage,
        ]);
    }

    /**
     * Indicate that the opportunity has high probability.
     */
    public function highProbability(): static
    {
        return $this->state(fn (array $attributes) => [
            'probability' => $this->faker->numberBetween(80, 100),
        ]);
    }

    /**
     * Indicate that the opportunity has high value.
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'expected_value' => $this->faker->randomFloat(2, 500000, 1000000),
        ]);
    }

    /**
     * Indicate that the opportunity is closing soon.
     */
    public function closingSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'expected_close_date' => $this->faker->dateTimeBetween('now', '+30 days'),
        ]);
    }

    /**
     * Indicate that the opportunity is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'expected_close_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'status' => 'activa',
        ]);
    }
}
