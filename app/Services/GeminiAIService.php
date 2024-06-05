<?php
// app/Services/GeminiAIService.php
namespace App\Services;

use GuzzleHttp\Client;

class GeminiAIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GEMINI_AI_API_KEY'); // Simpan API Key di file .env
    }

    public function sendMessage($message)
    {
        $response = $this->client->post('https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $message]
                        ]
                    ]
                ],
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
