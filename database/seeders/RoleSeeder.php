<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleNames = ['admin', 'editor'];

        foreach ($roleNames as $roleName) {
            $role = Role::create(['name' => $roleName]);

            $user = User::inRandomOrder()->first();
            $user->roles()->attach($role->id);
        }
    }
}
