<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Traits\ChatbotInteractionTrait;

class BotController extends Controller
{
    use ChatbotInteractionTrait;

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
        $message = $request->input('message');
        $userId = Auth::id();
        //userId tostring
        $userId = strval($userId);
        $apikey = env('COZE_API_KEY');
        $isNewChat = $request->input('new_chat', false);
        
        $chatHistoryKey = 'chat_history_' . $userId;
        $chatHistory = Cache::get($chatHistoryKey, []);
        $conversationId = Cache::get('conversation_id_' . $userId);

        if ($isNewChat) {
            Cache::forget($chatHistoryKey);
            Cache::forget($conversationId);
        }

        $chatHistory[] = [
            'role' => 'user',
            'content' => $message,
            'content_type' => 'text'
        ];

        $data = [
            'bot_id' => env('BOT_COZE_API_KEY'),
            'user' => $userId,
            'query' => $message,
            'stream' => false,
            'chat_history' => $chatHistory,
            'conversation_id' => $conversationId
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apikey,
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'Host' => 'api.coze.com',
            'Connection' => 'keep-alive'
        ])->post('https://api.coze.com/open_api/v2/chat', $data);

        if ($response->successful()) {
            $responseData = $response->json();
            
            if ($this->isValidResponse($responseData)) {
                $this->incrementChatbotInteraction();

                foreach ($responseData['messages'] as $botMessage) {
                    if ($botMessage['type'] === 'answer') {
                        $chatHistory[] = [
                            'role' => 'assistant',
                            'type' => 'answer',
                            'content' => $botMessage['content'],
                            'content_type' => 'text'
                        ];
                    }
                }

                Cache::put($chatHistoryKey, $chatHistory, now()->addDays(1));
                Cache::put('conversation_id_' . $userId, $responseData['conversation_id'], now()->addDays(1));

                return response()->json($responseData);
            } else {
                return response()->json(['error' => 'Invalid response from bot'], 500);
            }
        } else {
            return response()->json(['error' => 'Request failed'], $response->status());
        }
    }

    private function isValidResponse($response)
    {
        return isset($response['messages']) && is_array($response['messages']) && !empty($response['messages']) && $response['code'] === 0 && $response['msg'] === 'success';
    }

    public function resetChat()
    {
        $userId = Auth::id();
        $chatHistoryKey = 'chat_history_' . $userId;
        $conversationId = 'conversation_id_' . $userId;

        Cache::forget($chatHistoryKey);
        Cache::forget($conversationId);

        return response()->json(['message' => 'Chat history has been reset']);
    }
}
