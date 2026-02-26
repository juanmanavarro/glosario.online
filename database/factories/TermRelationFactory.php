<?php

namespace Database\Factories;

use App\Enums\TermRelationType;
use App\Models\Term;
use App\Models\TermRelation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TermRelation>
 */
class TermRelationFactory extends Factory
{
    protected $model = TermRelation::class;

    public function definition(): array
    {
        return [
            'term_id' => Term::factory(),
            'related_term_id' => Term::factory(),
            'relation_type' => fake()->randomElement(TermRelationType::cases()),
        ];
    }
}
