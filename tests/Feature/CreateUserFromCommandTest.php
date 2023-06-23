<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Console\Command;
use Tests\TestCase;

class CreateUserFromCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_user_can_be_created_with_valid_data()
    {
        $this->artisan('user:create')
            ->expectsQuestion('Name of the new user', 'John Doe')
            ->expectsQuestion('Email of the new user', 'test@email.com')
            ->expectsQuestion('Password of the new user', 'password')
            ->expectsChoice('Role of the new user', 'admin', ['admin', 'editor'])
            ->expectsOutput('User created successfully')
            ->assertExitCode(Command::SUCCESS);

        $user = User::first();
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'test@email.com',
        ]);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertTrue($user->roles->contains('name', 'admin'));
    }

    public function test_user_cannot_be_created_with_invalid_data()
    {
        $this->artisan('user:create')
            ->expectsQuestion('Name of the new user', '')
            ->expectsQuestion('Email of the new user', 'invalid-email')
            ->expectsQuestion('Password of the new user', 'short')
            ->expectsChoice('Role of the new user', 'invalid-role', ['admin', 'editor'])
            ->expectsOutput('Role not found')
            ->assertExitCode(Command::INVALID);

        $this->assertDatabaseCount('users', 0);
    }
}
