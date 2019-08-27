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
        $check_like = auth()->user()->isLiking($tweet->id);
        if($check_like&&is_null($check_like->deleted_at))
        {
            $check_like->delete();
            return response()->json(["message" => "unlike created success", "code" => 200], 200);
        }
        elseif($check_like&&$check_like->deleted_at)
        {
            $check_like->restore();
            return response()->json(["message" => "like restore success", "code" => 200], 200);
        }
        elseif(is_null($check_like))
        {
            Like::create([
                'tweet_id' => $tweet->id,
                'user_id' => auth()->user()->id
            ]);
            return response()->json(["message" => "like created success", "code" => 200], 200);
        }
    }
}
