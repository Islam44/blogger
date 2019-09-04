<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;


class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $rules =
            [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:5|max:255
                           regex:/[a-z]/|regex:/[A-Z]/|
                           regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:1024
                        dimensions:min_width=400,min_height=400,ratio=1/1'
            ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails())
        {
            return response()->json(["error"=> $validator->messages(),"code"=> 422],422);
        }
        $data= $request->all();
        $data['name']= $request->name;
        $data['email']= $request->email;
        $data['password']= bcrypt($request->password);
        $data['image']= $request->image->store('images');

        $user= User::create($data);
        $msg=
            [
            'msg1' => trans('messages.msg1'),
            'msg2' => trans('messages.msg2'),
            ];
        return response()->json([
            "welcome_message1" =>$msg['msg2']." ".$user->name,
            "welcome_message2" =>$msg['msg1'],
            "user" => $user],200);
    }

    public function login(Request $request)
    {
        $rules=
            [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
            ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails())
        {
            return response()->json(["error"=> $validator->messages(),"code"=> 422],422);
        }
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
        {
            return response()->json(["message" => "Unauthorized","code"=> 401], 401);
        }
        $user= $request->user();
        $tokenResult= $user->createToken('Personal Access Token');
        $token= $tokenResult->token;
        if($request->remember_me)
        {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            "access_token" => $tokenResult->accessToken,
            "token_type" => "Bearer",
            "expires_at" =>Carbon::now()->addWeeks(1)->toDateTimeString()
        ],200);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        Cache::forget('feed-tweets');
        return response()->json([
            "message"=> "Successfully logged out"
        ],200);
    }

    public function profile()
    {
        $user=Auth::user();
        $tweets= $user->tweets()->with('comments')->with('likes')->get();
        return response()->json([
            "user"=> $user,
            "tweets"=> $tweets,
        ],200);
    }
}
