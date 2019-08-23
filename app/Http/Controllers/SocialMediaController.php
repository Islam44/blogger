<?php

namespace App\Http\Controllers;

use App\User;
use App\Tweet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class SocialMediaController extends Controller
{
    public function index()
    {
        $users= User::where('id','!=',auth()->user()->id)->get();
        return response()->json(["users"=> $users],200);
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
            return response()->json(["message" => "You are now following {$user->name}"],200);
        }
        else return response()->json(["message" => "You are already following {$user->name}","code"=> 401], 401);
    }

    public function un_follow(User $user)
    {
        if(auth()->user()->isFollowing($user->id))
        {
            auth()->user()->un_follow($user->id);
            return response()->json(["message"=> "You are now unfollow {$user->name}"],200);
        }
        else if(auth()->user()->id== $user->id)
        {
            return response()->json(["message"=> "You cant unfollow yourself","code"=> 401], 401);
        }

        else
            return response()->json(["message" => "You are already unfollowing {$user->name}","code"=> 401], 401);

    }

    public function timeline()
        {
            $followsIds= DB::table('follower')
                ->where('user_id',auth()->user()->id)
                ->pluck('follows_id')
                ->toArray();
            $followsIds[]= auth()->user()->id;
            $tweets= Tweet::whereIn('user_id', $followsIds)
                ->latest()
                ->with('comments')
                ->with('likes')
                ->with('user')
                ->get();
            $rules= ['per_page' => 'integer|min:5|max:10'];
            Validator::validate(request()->all(),$rules);
            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage =5;//default
            if(request()->has('per_page'))
            {
                $perPage = (int)request()->per_page;
            }
            $results = $tweets->slice(($page - 1) * $perPage,$perPage)->values();
            $paginated= new LengthAwarePaginator($results, $tweets->count(), $perPage, $page,
                [
                'path'=> LengthAwarePaginator::resolveCurrentPath(),
                ]);
            $paginated->appends(request()->all());
            return response()->json(["data"=> $paginated],200);
        }
}
