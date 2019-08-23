<?php

namespace App\Http\Controllers;

use App\Like;
use App\Tweet;
use Illuminate\Http\Request;

class LikeController extends Controller
{

    public function isLikedByMe(Tweet $tweet)
    {
        if(Like::where('user_id','=',auth()->user()->id)->where('tweet_id','=',$tweet->id)->exists()) {
            return response()->json(["message" => true, "code" => 200], 200);
        }
        return response()->json(["message" => false, "code" => 200], 200);
    }

    public function like_unlike(Tweet $tweet)
    {
       $check_like= auth()->user()->isLiking($tweet->id);
        if(is_null($check_like))
        {
            Like::create([
                'tweet_id' => $tweet->id,
                'user_id' => auth()->user()->id
            ]);
            return response()->json(["message" => "like created success", "code" => 200], 200);
        }
        else {
                $check_like->delete();
                return response()->json(["message" => "unlike created success", "code" => 200], 200);
            }

    }
}
