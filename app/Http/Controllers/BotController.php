<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BotController extends Controller
{
    public function __construct()
    {
        //tambahkan middleware auth dan limitchatbot
        $this->middleware('auth');
        $this->middleware('limit.chatbot');
    }

    public function index()
    {
        if(Auth::check()){
            $user = Auth::user();
            $key = 'chatbot_limit_' . $user->id;
            $limit = 30;

            $interaction = Cache::get($key, 0);

            $remainingInteractions = $limit - $interaction;
            
            return view('page.bot.index', compact('remainingInteractions'));
        }
    }

    public function sendMessage(Request $request)
    {
        $message = $request->message;
        $apikey = env('COZE_API_KEY');
        // Data yang akan dikirim dalam permintaan POST
        $data = [
            'bot_id' => env('BOT_COZE_API_KEY'), // Ganti dengan Bot ID Anda yang sebenarnya
            'user' => '29032201862555',
            'query' => $message,
            'stream' => false
        ];

        // Mengirim permintaan POST dengan header yang sesuai
        $response = Http::withHeaders([
            'Authorization' => 'Bearer'. $apikey, // Ganti dengan Personal Access Token Anda yang sebenarnya
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'Host' => 'api.coze.com',
            'Connection' => 'keep-alive'
        ])->post('https://api.coze.com/open_api/v2/chat', $data);

        // Menangani respons
        if ($response->successful()) {
            // Mengambil dan mengembalikan data respons jika permintaan berhasil
            $responseData = $response->json();
            return response()->json($responseData);
        } else {
            // Menangani error jika permintaan gagal
            return response()->json(['error' => 'Request failed'], $response->status());
        }
    }
}
