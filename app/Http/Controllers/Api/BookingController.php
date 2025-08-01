<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller 
{
public function index(){

      $user = Auth::user();

       $bookings = Booking::with('event')  // eager load the event
    ->where('user_id', $user->id)
    ->get();
    // $bookings=Booking::all();

    

    return response()->json([
        'status'=>true,
        'bookings'=>$bookings,
    ]);
}


public function store(Request $request)
{
    $request->validate([
        'event_id' => 'required|exists:events,id',
    ]);

    $user = Auth::user();
    $event_id = $request->input('event_id');

    // تحقق وجود الفعالية وعدد المقاعد قبل أي شيء
    $event = Event::find($event_id);
    if (!$event) {
        return response()->json(['message' => 'Event does not exist.'], 404);
    }
    if ($event->available_seats <= 0) {
        return response()->json(['message' => 'No available seats.'], 400);
    }

    // تحقق إذا المستخدم حجز نفس الفعالية سابقاً
    $existing = Booking::where('user_id', $user->id)
                ->where('event_id', $event_id)
                ->first();

    if ($existing) {
        if ($existing->status === 'success') {
            // تم الحجز مسبقاً بنجاح، منع التكرار
            return response()->json([
                'message' => 'You have already booked this event.'
            ], 409);
        } elseif ($existing->status === 'canceled') {
            // لو ملغى الحجز، نحذف السجل القديم
            $existing->delete();
            // ثم ننشئ حجز جديد
        } else {
            // حالة أخرى ممكن تتعامل معها حسب النظام، أو تمنع التكرار
            return response()->json([
                'message' => 'You have a booking in process or invalid status.'
            ], 409);
        }
    }

    // إنشاء الحجز الجديد
    $booking = Booking::create([
        'user_id' => $user->id,
        'event_id' => $event_id,
        'status' => 'success', // أو حسب منطقك: 'pending' مثلاً
    ]);

    // نقص عدد المقاعد المتاحة
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
