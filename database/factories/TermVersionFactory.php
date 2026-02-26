<?php

namespace Database\Factories;

use App\Models\Term;
use App\Models\TermVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TermVersion>
 */
class TermVersionFactory extends Factory
{
    protected $model = TermVersion::class;

    public function definition(): array
    {
        return [
            'term_id' => Term::factory(),
            'language_code' => 'es',
            'title' => ucfirst(fake()->words(fake()->numberBetween(2, 4), true)),
            'definition' => fake()->paragraphs(fake()->numberBetween(2, 4), true),
            'notes' => fake()->optional()->paragraph(),
            'created_by' => null,
            'reviewed_by' => null,
            'approved_at' => null,
            // version_number is auto-assigned by observer.
        ];
    }
}
