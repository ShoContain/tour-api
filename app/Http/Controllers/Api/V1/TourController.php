<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use Illuminate\Http\Request;
use App\Models\Travel;

class TourController extends Controller
{

    public function index(Travel $travel)
    {
        $tours = $travel->tours()->orderBy('starting_date')->paginate(config('app.paginationPerPage.tours'));
        return TourResource::collection($tours);
    }
}
