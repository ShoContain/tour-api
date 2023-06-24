<?php

namespace Tests\Traits;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;

trait TestUser
{
    protected function editorUser(): User
    {
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();
        $role = Role::where('name', 'editor')->first();
        $user->roles()->attach($role);

        return $user;
    }


    protected function adminUser(): User
    {
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();
        $role = Role::where('name', 'admin')->first();
        $user->roles()->attach($role);

        return $user;
    }
}
