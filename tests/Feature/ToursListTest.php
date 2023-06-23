<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToursListTest extends TestCase
{
    use RefreshDatabase;

    public function test_tours_list_by_travel_slug_returns_correct_tours(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id]);

        $response = $this->get("/api/v1/travels/$travel->slug/tours");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $tour->id);
    }

    public function test_tour_price_is_shown_correctly(): void
    {
        $travel = Travel::factory()->create();
        Tour::factory()->create(['travel_id' => $travel->id, 'price' => 2000]);

        $response = $this->get("/api/v1/travels/$travel->slug/tours");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => '2,000.00']);
    }

    public function test_tours_list_returns_pagination(): void
    {
        $toursPerPage = config('app.paginationPerPage.tours');

        $travel = Travel::factory()->create();
        Tour::factory($toursPerPage + 1)->create(['travel_id' => $travel->id]);

        $response = $this->get("/api/v1/travels/$travel->slug/tours");

        $response->assertStatus(200);
        $response->assertJsonCount($toursPerPage, 'data');
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.last_page', 2);
    }

    public function test_tours_list_sorts_by_starting_date_correctly(): void
    {
        $travel = Travel::factory()->create();
        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3)
        ]);
        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1)
        ]);

        $response = $this->get("/api/v1/travels/$travel->slug/tours");

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $earlierTour->id);
        $response->assertJsonPath('data.1.id', $laterTour->id);
    }

    public function test_tours_list_sorts_by_price_correctly()
    {
        $travel = Travel::factory()->create();
        $cheaperTour = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 1000]);
        $expensiveTour = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 2000]);

        $response = $this->get("/api/v1/travels/$travel->slug/tours?sortBy=price&sortDirection=desc");

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $expensiveTour->id);
        $response->assertJsonPath('data.1.id', $cheaperTour->id);

        $response = $this->get("/api/v1/travels/$travel->slug/tours?sortBy=price&sortDirection=asc");

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $cheaperTour->id);
        $response->assertJsonPath('data.1.id', $expensiveTour->id);
    }

    public function test_tours_list_filter_by_date()
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3)
        ]);

        $response = $this->get("/api/v1/travels/$travel->slug/tours?dateFrom=" . now()->addDays(1)->format('Y-m-d'));

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $tour->id);

        $response = $this->get("/api/v1/travels/$travel->slug/tours?dateTo=" . now()->addDays(1)->format('Y-m-d'));

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function test_tours_list_filter_by_price()
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 2000]);

        $response = $this->get("/api/v1/travels/$travel->slug/tours?priceFrom=1000");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $tour->id);

        $response = $this->get("/api/v1/travels/$travel->slug/tours?priceTo=1000");

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function test_tours_list_returns_validation_error_if_sort_by_or_sort_direction_is_invalid()
    {
        $travel = Travel::factory()->create();
        $endpoint = "/api/v1/travels/$travel->slug/tours";

        $response = $this->get($endpoint . "?sortBy=hoge");
        $response->assertStatus(422);

        $response = $this->get($endpoint . "?sortDirection=abcdefg");
        $response->assertStatus(422);
    }
}
