<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedCategories();
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
            'admin' => tap($this->firstOrCreateSeedUser('Admin User', 'admin@example.com'), fn (User $user) => $user->syncRoles([$roles[$adminRoleName]])),
            'editor' => tap($this->firstOrCreateSeedUser('Editor User', 'editor@example.com'), fn (User $user) => $user->syncRoles([$roles['editor']])),
            'reviewer' => tap($this->firstOrCreateSeedUser('Reviewer User', 'reviewer@example.com'), fn (User $user) => $user->syncRoles([$roles['reviewer']])),
            'contributor' => tap($this->firstOrCreateSeedUser('Contributor User', 'contributor@example.com'), fn (User $user) => $user->syncRoles([$roles['contributor']])),
        ];
    }

    private function firstOrCreateSeedUser(string $name, string $email): User
    {
        return User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );
    }

    private function seedCategories()
    {
        return collect([
            Category::firstOrCreate(
                ['slug' => 'sin-categoria'],
                [
                    'name' => 'Sin categoria',
                    'description' => null,
                    'parent_id' => null,
                ]
            ),
        ]);
    }
}
