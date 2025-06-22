<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
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

    // minus available_seats with 1 
    $event_id=$request->input('event_id');
    $event=Event::find($event_id);

    if (!$event){
        return response()->json(['message'=>'event does\'nt exists'],404);
    }
    if ($event->available_seats<=0){
        return response()->json(['message'=>'no available seats'],400);
    }
    
    $event->available_seats--;
    $event->save();

    return response()->json([
        'message' => 'Booking created successfully.',
        'booking' => $booking,
    ], 201);
}

public function cancel(Request $request){

    

    $request->validate([

        'event_id'=>'required|exists:events,id',

    ]);

    $user=$request->user();
    $event_id=$request->event_id;


    $booking=Booking::where('user_id',$user->id)
    ->where('event_id',$event_id)
    ->where('status','success')
    ->first();

    if(!$booking){
        return response()->json([
            'status'=>false,
            'message'=>'this booking not available'

        ],404);
    }

    $booking->status='canceled';
    $booking->save();
    $event=Event::find($event_id);

    $event->available_seats++;
    $event->save();



    return response()->json([
        'status'=>true,
        'message'=>'booking canceled succussfully'
    ],201);





}
}
