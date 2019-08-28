<?php

namespace App\Http\Controllers;

use App\Like;
use App\Notifications\LikeNotification;
use App\Notifications\PressLike;
use App\Tweet;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

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
            DB::transaction(function() use($tweet){
                Like::create([
                    'tweet_id' => $tweet->id,
                    'user_id' => auth()->user()->id
               ]);
            $tweet_who_liked= Tweet::where('id','=',$tweet->id)->first();
            $user_who_tweet=$tweet_who_liked->user;
            if($user_who_tweet->id!=auth()->user()->id)
              {
                  Notification::send($user_who_tweet, new PressLike(auth()->user(),$tweet_who_liked));
              }
            });
            return response()->json(["message" => "like created success", "code" =>200], 200);
        }
    }
}
