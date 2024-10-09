<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $role = Role::create([
            'name' => UserRole::ADMIN->value,
        ]);
        $permission = Permission::create([
            'name' =>  UserPermission::ADMIN_PERMISSIONS->value,
        ]);
        $role->permissions()->attach($permission);
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin123123!!',
            'role_id' => $role->id,
        ]);
    }
}
