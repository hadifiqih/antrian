<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BotController extends Controller
{

    public function index()
    {
        return view('page.bot.index');
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
