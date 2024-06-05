<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\GeminiAIService;

class BotController extends Controller
{

    public function index()
    {
        return view('page.bot.index');
    }

    public function sendMessage(Request $request)
    {
        $botId = env('BOT_COZE_API_KEY'); // Replace with your Coze bot ID
        $userId = '29032201862555'; // Replace with the user ID
        $message = $request->message;
        $personalAccessToken = env('COZE_API_KEY'); // Replace with your token

        $client = new Client();

        $body = [
            'bot_id' => $botId,
            'user' => $userId,
            'query' => $message,
            'stream' => false,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $personalAccessToken,
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
        ];

        try {
            $response = $client->post('https://api.coze.com/open_api/v2/chat', [
                'headers' => $headers,
                'json' => $body,
            ]);

            // Handle the response, e.g., decode JSON data, check for errors
            $responseData = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 200) {
                // Success! Process the response data
                return response()->json($responseData);
            } else {
                // Handle errors based on status code and response data
                return response()->json(['error' => 'API request failed'], $response->getStatusCode());
            }
        } catch (Exception $e) {
            // Handle exceptions during the request
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
