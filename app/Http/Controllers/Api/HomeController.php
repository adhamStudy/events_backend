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

    $categories = Category::all()->keyBy('id'); // for quick lookup
    $events = Event::all();
    $cities = City::all()->keyBy('id'); // for quick lookup

    foreach ($events as $event) {
        $event->city_name = $cities[$event->city_id]->name ?? null;
        $event->category_name = $categories[$event->category_id]->name ?? null;
    }

    // Get all event_ids the user has booked
    $bookedEventIds = $user->bookings()
                        ->where('status', 'success')
                        ->pluck('event_id')
                        ->toArray();

    // Add booked flag to each event
    $events = $events->map(function ($event) use ($bookedEventIds) {
        $event->booked = in_array($event->id, $bookedEventIds);
        return $event;
    });

    return [
        'message' => 'success',
        'categories' => $categories->values(),
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
