<?php

namespace Database\Seeders;

use App\Enums\TermRelationType;
use App\Models\Category;
use App\Models\Keyword;
use App\Models\Term;
use App\Models\TermRelation;
use App\Models\TermVersion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = $this->seedUsers();
        $categories = $this->seedCategories();
        $terms = $this->seedTermsWithVersions($users, $categories);

        $this->seedKeywords($terms);
        $this->seedRelations($terms);
    }

    private function seedUsers(): array
    {
        $roles = collect([
            config('filament-shield.super_admin.name', 'super_admin'),
            'editor',
            'reviewer',
            'contributor',
        ])->mapWithKeys(fn (string $name) => [
            $name => Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]),
        ]);

        $adminRoleName = config('filament-shield.super_admin.name', 'super_admin');

        return [
            'admin' => tap(User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
            ]), fn (User $user) => $user->assignRole($roles[$adminRoleName])),
            'editor' => tap(User::factory()->create([
                'name' => 'Editor User',
                'email' => 'editor@example.com',
            ]), fn (User $user) => $user->assignRole($roles['editor'])),
            'reviewer' => tap(User::factory()->create([
                'name' => 'Reviewer User',
                'email' => 'reviewer@example.com',
            ]), fn (User $user) => $user->assignRole($roles['reviewer'])),
            'contributor' => tap(User::factory()->create([
                'name' => 'Contributor User',
                'email' => 'contributor@example.com',
            ]), fn (User $user) => $user->assignRole($roles['contributor'])),
        ];
    }

    private function seedCategories()
    {
        $categories = Category::factory()->count(10)->create();

        foreach ($categories->slice(4) as $category) {
            $category->update([
                'parent_id' => $categories->random(4)->random()->id,
            ]);
        }

        return $categories->fresh();
    }

    private function seedTermsWithVersions(array $users, $categories)
    {
        $publishedCount = 30;
        $terms = collect();

        for ($i = 1; $i <= 50; $i++) {
            $term = Term::factory()->create([
                'slug' => Str::slug('termino fitopatologia '.$i),
            ]);

            $isPublished = $i <= $publishedCount;

            $version = TermVersion::factory()->create([
                'term_id' => $term->id,
                'language_code' => 'es',
                'title' => 'Término fitopatológico '.$i,
                'definition' => 'Definición de referencia para el término '.$i.'. '.fake()->paragraphs(2, true),
                'created_by' => $users['contributor']->id,
                'reviewed_by' => $isPublished ? $users['reviewer']->id : null,
                'approved_at' => $isPublished ? now()->subDays(fake()->numberBetween(0, 90)) : null,
            ]);

            if (! $isPublished) {
                // Keep draft terms explicitly consistent.
                $term->update([
                    'current_version_id' => null,
                ]);
            }

            $term->categories()->syncWithoutDetaching(
                $categories->random(fake()->numberBetween(1, 3))->pluck('id')->all()
            );

            if (fake()->boolean(20)) {
                $term->media()->create([
                    'type' => fake()->randomElement(['image', 'audio', 'pdf']),
                    'path' => 'media/terms/'.$term->id.'/archivo-1'.fake()->fileExtension(),
                    'caption' => fake()->optional()->sentence(),
                ]);
            }

            $terms->push($term->fresh(['currentVersion']));
        }

        return $terms;
    }

    private function seedKeywords($terms): void
    {
        foreach ($terms as $term) {
            $keywords = collect([
                str_replace('-', ' ', $term->slug),
                fake()->word(),
                fake()->word(),
                'fitopatología',
            ])->unique()->take(fake()->numberBetween(2, 3));

            foreach ($keywords as $keyword) {
                Keyword::firstOrCreate([
                    'term_id' => $term->id,
                    'keyword' => Str::lower($keyword),
                ]);
            }
        }
    }

    private function seedRelations($terms): void
    {
        $types = TermRelationType::cases();
        $target = 80;
        $created = 0;
        $attempts = 0;
        $maxAttempts = 500;

        while ($created < $target && $attempts < $maxAttempts) {
            $attempts++;
            $source = $terms->random();
            $related = $terms->random();

            if ($source->id === $related->id) {
                continue;
            }

            $relation = TermRelation::firstOrCreate([
                'term_id' => $source->id,
                'related_term_id' => $related->id,
                'relation_type' => fake()->randomElement($types),
            ]);

            if ($relation->wasRecentlyCreated) {
                $created++;
            }
        }
    }
}
