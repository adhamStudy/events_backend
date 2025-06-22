<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller 
{
    public function store(Request $request)
{
    $request->validate([
        'event_id' => 'required|exists:events,id',
    ]);

    $user = Auth::user();

    // تأكد أنه ما حجز نفس الفعالية من قبل
    $existing = Booking::where('user_id', $user->id)
                ->where('event_id', $request->event_id)
                ->first();

    if ($existing) {
        return response()->json([
            'message' => 'You have already booked this event.'
        ], 409);
    }

    // إنشاء الحجز
    $booking = Booking::create([
        'user_id' => $user->id,
        'event_id' => $request->event_id,
        'status' => 'success', // أو pending حسب النظام
    ]);
    

    return response()->json([
        'message' => 'Booking created successfully.',
        'booking' => $booking,
    ], 201);
}
}
