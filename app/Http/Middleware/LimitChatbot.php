<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LimitChatbot
{
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()){
            $user = Auth::user();
            $key = 'chatbot_limit_' . $user->id;
            $limit = 30;

            $interaction = Cache::get($key, 0);

            if($request->isMethod('post')){
                if($interaction >= $limit){
                    return response()->json([
                        'message' => 'Anda telah mencapai batas interaksi per hari. Silahkan coba lagi besok.'
                    ], 429);
                }
            }

            $remainingInteractions = $limit - $interaction;
            View::share('remainingInteractions', $remainingInteractions);
        }

        return $next($request);
    }
}
