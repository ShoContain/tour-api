<?php

namespace Tests\Feature;

use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Traits\TestUser;

class AdminTourTest extends TestCase
{
    use RefreshDatabase, TestUser;

    private $postEndpoint = '';
    private $getEndpoint = '';

    protected function setUp(): void
    {
        parent::setUp();

        $travel = Travel::factory()->create();
        $this->postEndpoint = '/api/v1/admin/travels/' . $travel->id . '/tours';
        $this->getEndpoint = '/api/v1/travels/' . $travel->slug . '/tours';
    }

    /**
     * @dataProvider validData
     */
    public function test_public_user_cannot_access_creating_tour($data): void
    {
        $response = $this->postJson($this->postEndpoint, $data);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @dataProvider validData
     */
    public function test_non_admin_user_cannot_access_creating_tour($data): void
    {
        $editorUser = $this->editorUser();
        $response = $this->actingAs($editorUser)->postJson($this->postEndpoint, $data);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @dataProvider validData
     */
    public function test_admin_user_can_access_and_create_a_tour($data): void
    {
        $adminUser = $this->adminUser();
        $response = $this->actingAs($adminUser)->postJson($this->postEndpoint, $data);
        $response->assertStatus(Response::HTTP_CREATED);

        $response = $this->get($this->getEndpoint);
        $response->assertJsonFragment(['name' => $data['name']]);
    }

    /**
     * @dataProvider inValidData
     */
    public function test_travel_cannot_be_created_with_invalid_data($data): void
    {
        $adminUser = $this->adminUser();
        $response = $this->actingAs($adminUser)->postJson($this->postEndpoint, $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function validData()
    {
        return [
            [[
                'name' => 'my travel',
                'starting_date' => '2023-01-01',
                'ending_date' => '2023-01-02',
                'price' => 1000,
            ]],
            [[
                'name' => 'my travel 2',
                'starting_date' => '2021-01-01',
                'ending_date' => '2021-01-02',
                'price' => 500,
            ]],
        ];
    }

    public function inValidData()
    {
        return [
            [[
                'name' => '',
                'starting_date' => '2023-01-01',
                'ending_date' => '2023-01-02',
                'price' => 1000,
            ]],
            [[
                'name' => 'my travel',
                'starting_date' => '2021-0-02',
                'ending_date' => '2021-01-01',
                'price' => 500,
            ]],
            [[
                'name' => 'my travel',
                'starting_date' => '2021-0-02',
                'ending_date' => '2021-01-01',
                'price' => '',
            ]],
        ];
    }
}
