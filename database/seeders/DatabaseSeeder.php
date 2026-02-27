<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
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
        $superAdminRoleName = config('filament-shield.super_admin.name', 'super_admin');

        $superAdminRole = Role::firstOrCreate([
            'name' => $superAdminRoleName,
            'guard_name' => 'web',
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $editorRole = Role::firstOrCreate([
            'name' => 'Editor',
            'guard_name' => 'web',
        ]);

        $memberRole = Role::firstOrCreate([
            'name' => 'member',
            'guard_name' => 'web',
        ]);

        $permissions = Permission::query()->where('guard_name', 'web')->get();

        $superAdminRole->syncPermissions($permissions);

        $adminRole->syncPermissions(
            $permissions->filter(fn (Permission $permission) => Str::endsWith($permission->name, [
                ':User',
                ':Term',
                ':Category',
                ':TermVersion',
                ':EditorialLog',
            ]))
        );

        $editorRole->syncPermissions(
            $permissions->filter(function (Permission $permission): bool {
                if (Str::endsWith($permission->name, [':Term', ':Category', ':TermVersion'])) {
                    return true;
                }

                return Str::endsWith($permission->name, ':EditorialLog')
                    && Str::startsWith($permission->name, ['ViewAny:', 'View:']);
            })
        );

        return [
            'superadmin' => tap(
                $this->firstOrCreateSeedUser('Superadmin User', 'superadmin@example.com'),
                fn (User $user) => $user->syncRoles([$superAdminRole])
            ),
            'admin' => tap(
                $this->firstOrCreateSeedUser('Admin User', 'admin@example.com'),
                fn (User $user) => $user->syncRoles([$adminRole])
            ),
            'editor' => tap(
                $this->firstOrCreateSeedUser('Editor User', 'editor@example.com'),
                fn (User $user) => $user->syncRoles([$editorRole])
            ),
            'member' => tap(
                $this->firstOrCreateSeedUser('Member User', 'member@example.com'),
                fn (User $user) => $user->syncRoles([$memberRole])
            ),
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
