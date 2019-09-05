<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Tweet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:delete,comment')->only('destroy');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Tweet $tweet)
    {
        $rules =
            [
            'text' => 'required|min:1|max:50'
            ];
        $validator= Validator::make($request->all(), $rules);
        if($validator->fails())
        {
           return response()->json(["error"=> $validator->messages(),"code"=> 422], 422);
        }
        $data = $request->all();
        $data['text'] =$request->text;
        $data['user_id'] =auth()->user()->id;
        $data['tweet_id'] =$tweet->id;
        $comment =Comment::create($data);
        return response()->json([
            "comment"=> $comment,
            "code"=>200
        ],200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        if($comment && auth()->user()->id== $comment->user_id)
        {
            $comment->delete();
            return response()->json([
                "message"=> "Comment was deleted"
            ],200);
        }
        else
        {
            return response()->json(["error" => "Unauthorized", "code" =>403], 403);
        }
    }
}
