<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use App\Http\Requests\TourListRequest;
use App\Models\Travel;

class TourController extends Controller
{

    public function index(Travel $travel, TourListRequest $request)
    {
        $tours = $travel->tours()
            ->when($request->has('dateFrom'), function ($query) use ($request) {
                $query->where('starting_date', '>=', $request->dateFrom);
            })
            ->when($request->has('dateTo'), function ($query) use ($request) {
                $query->where('ending_date', '<=', $request->dateTo);
            })
            ->when($request->has('priceFrom'), function ($query) use ($request) {
                $query->where('price', '>=', $request->priceFrom * 100);
            })
            ->when($request->has('priceTo'), function ($query) use ($request) {
                $query->where('price', '<=', $request->priceTo * 100);
            })
            ->when($request->has('sortBy') && $request->has('sortDirection'), function ($query) use ($request) {
                $query->orderBy($request->sortBy, $request->sortDirection);
            })
            ->orderBy('starting_date')
            ->paginate(config('app.paginationPerPage.tours'));

        return TourResource::collection($tours);
    }
}
