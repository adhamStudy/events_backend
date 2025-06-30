<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // جلب قائمة المزودين (Providers) - للزائر لاختيار من سيرسل له
    public function getProviders()
    {
        $providers = Provider::select('id', 'name', 'company_name')->get();

        return response()->json($providers);
    }

    // إرسال رسالة من الزائر لمزود معين
    public function sendMessage(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $user = Auth::user();

        $message = Message::create([
            'user_id' => $user->id,
            'provider_id' => $request->provider_id,
            'title' => $request->title,
            'body' => $request->body,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message,
        ], 201);
    }

    // المزود يسترجع كل الرسائل الموجهة له مع حالة كل رسالة
    public function getMessages()
    {
        $provider = Auth::user()->provider;

        if (!$provider) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = Message::where('provider_id', $provider->id)
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($messages);
    }

    // المزود يغير حالة رسالة معينة (قبول، تجاهل، ...الخ)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,success,ignored',
        ]);

        $provider = Auth::user()->provider;
        if (!$provider) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::where('id', $id)
            ->where('provider_id', $provider->id)
            ->firstOrFail();

        $message->status = $request->status;
        $message->save();

        return response()->json(['message' => 'Status updated', 'data' => $message]);
    }
}
