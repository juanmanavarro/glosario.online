<?php

namespace Database\Factories;

use App\Enums\TermStatus;
use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Term>
 */
class TermFactory extends Factory
{
    protected $model = Term::class;

    public function definition(): array
    {
        $base = fake()->unique()->words(fake()->numberBetween(1, 3), true);

        return [
            'slug' => Str::slug($base),
            'status' => TermStatus::Draft,
            'current_version_id' => null,
            'published_at' => null,
        ];
    }
}
