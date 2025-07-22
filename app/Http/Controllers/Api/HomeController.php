<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Models\City;
use App\Models\Event;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $categories = Category::all();
        $events = Event::all();
        $cities = City::all()->keyBy('id'); // for faster lookup

        foreach ($events as $event) {
            $event->city_name = $cities[$event->city_id]->name ?? null;
        }

        // 1. Get all event_ids the user has booked
        $bookedEventIds = $user->bookings()
            ->where('status', 'success')
            ->pluck('event_id')
            ->toArray();

        // 2. Add booked flag to each event
        $events = $events->map(function ($event) use ($bookedEventIds) {
            $event->booked = in_array($event->id, $bookedEventIds);
            return $event;
        });

        // 3. Get all favorite category_ids for the user
        $favoriteCategoryIds = $user->favoriteCategories()
            ->pluck('category_id')
            ->toArray();

        // 4. Add isFav flag to each category (as boolean)
        $categories = $categories->map(function ($category) use ($favoriteCategoryIds) {
            $category->isFav = in_array($category->id, $favoriteCategoryIds);
            return $category;
        });

        return [
            'message' => 'success',
            'categories' => $categories,
            'events' => $events
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
