<?php

namespace App\Http\Controllers;

use auth;
use App\Tweet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TweetController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:delete,tweet')->only('destroy');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules =
            [
            'text' => 'required|min:1|max:140'
            ];
        $validator= Validator::make($request->all(), $rules);
        if($validator->fails())
        {
            return response()->json(["error"=> $validator->messages(),"code"=> 422], 422);
        }
        $data = $request->all();
        $data['text'] =$request->text;
        $data['user_id'] =auth()->user()->id;
        $tweet =Tweet::create($data);
        return response()->json([
           "tweet"=> $tweet
        ],200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tweet $tweet)
    {
       if(auth()->user()->id== $tweet->user_id)
       {
           $tweet->delete();
           return response()->json([
               "message"=> "Tweet was deleted"
           ],200);
       }
       else
           {
               return response()->json(["error" => "Unauthorized", "code" =>403], 403);
           }
    }
}
