<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_returns_token_with_valid_credentials()
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ]);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'invalid-password'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The provided credentials are incorrect.'
            ]);
    }
}
