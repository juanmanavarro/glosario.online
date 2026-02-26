<?php

namespace Database\Factories;

use App\Models\Keyword;
use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Keyword>
 */
class KeywordFactory extends Factory
{
    protected $model = Keyword::class;

    public function definition(): array
    {
        return [
            'term_id' => Term::factory(),
            'keyword' => fake()->unique()->word(),
        ];
    }
}
