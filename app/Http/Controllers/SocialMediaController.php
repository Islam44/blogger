<?php

namespace App\Http\Controllers;

use App\User;
use App\Tweet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class SocialMediaController extends Controller
{
    public function index()
    {
        $users= User::where('id','!=',auth()->user()->id)->get();
        return response()->json(["users"=> $users,"code"=> 200],200);
    }

    public function follow(User $user)
    {
        if(auth()->user()->id== $user->id)
        {
            return response()->json(["message"=> "You cant follow yourself","code"=> 401], 401);
        }
        if(!auth()->user()->isFollowing($user->id))
        {
            auth()->user()->follow($user->id);
            return response()->json(["message" => "You are now following {$user->name}","code"=> 200],200);
        }
        else return response()->json(["message" => "You are already following {$user->name}","code"=> 401], 401);
    }

    public function unFollow(User $user)
    {
        if(auth()->user()->isFollowing($user->id))
        {
            auth()->user()->unFollow($user->id);
            return response()->json(["message"=> "You are now unFollow {$user->name}","code"=> 200],200);
        }
        else if(auth()->user()->id== $user->id)
        {
            return response()->json(["message"=> "You cant unFollow yourself","code"=> 401], 401);
        }
        else
            return response()->json(["message" => "You are already unFollowing {$user->name}","code"=> 401], 401);

    }

    public function timeline()
        {
            $followsIds= DB::table('follower')
                ->where('user_id',auth()->user()->id)
                ->pluck('follows_id')
                ->toArray();
            $followsIds[]= auth()->user()->id;
            $perPage= 5;
            $tweets= Cache::remember('feed-tweets',30/60,function() use($followsIds,$perPage) {
               return Tweet::whereIn('user_id', $followsIds)
                    ->latest()
                    ->limit(10)
                    ->with('comments')
                    ->with('likes')
                    ->with('user')
                    ->paginate($perPage);
            });

            return response()->json(["data"=> $tweets,"code"=> 200],200);
        }
}
