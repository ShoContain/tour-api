<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Traits\TestUser;

class AdminTravelTest extends TestCase
{
    use RefreshDatabase, TestUser;

    private $endpoint = '/api/v1/admin/travels';

    public function test_public_user_cannot_access_creating_travel()
    {
        $response = $this->postJson($this->endpoint, [
            'is_public' => true,
            'name' => 'my travel',
            'description' => 'my travel description',
            'number_of_days' => 4
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_non_admin_user_cannot_access_creating_travel()
    {
        $editorUser = $this->editorUser();
        $response = $this->actingAs($editorUser)->postJson($this->endpoint, [
            'is_public' => true,
            'name' => 'my travel',
            'description' => 'my travel description',
            'number_of_days' => 4
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_user_can_access_creating_travel()
    {
        $adminUser = $this->adminUser();
        $response = $this->actingAs($adminUser)->postJson($this->endpoint, [
            'is_public' => true,
            'name' => 'my travel',
            'description' => 'my travel description',
            'number_of_days' => 4
        ]);
        $response->assertStatus(Response::HTTP_CREATED);

        $response = $this->get('/api/v1/travels');
        $response->assertJsonFragment([
            'name' => 'my travel',
            'description' => 'my travel description',
            'number_of_days' => 4
        ]);
    }

    public function test_travel_cannot_be_created_with_invalid_data()
    {
        $adminUser = $this->adminUser();
        $response = $this->actingAs($adminUser)->postJson($this->endpoint, [
            'is_public' => true,
            'name' => '',
            'description' => 'my travel description',
            'number_of_days' => 4
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
