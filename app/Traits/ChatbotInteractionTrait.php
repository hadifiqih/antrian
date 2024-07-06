<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

trait ChatbotInteractionTrait
{
    protected function incrementChatbotInteraction()
    {
        if(Auth::check()){
            $user = Auth::user();
            $key = 'chatbot_limit_' . $user->id;
            $limit = 30;

            $interaction = Cache::get($key, 0);

            if($interaction < $limit){
                Cache::increment($key);
                Cache::put($key, $interaction + 1, now()->endOfDay());
            }
        }
    }
}