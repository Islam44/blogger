<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
  //  return $request->user();
//});
    Route::post('signup', 'AuthController@signup');
    Route::post('login', 'AuthController@login')->name('login');
    Route::group([
        'middleware' => 'auth:api',
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('profile', 'AuthController@profile');
        Route::resource('tweets', 'TweetController', ['only' => ['store','destroy']]);
        Route::get('users', 'SocialMediaController@index');
        Route::get('users/{user}/follow', 'SocialMediaController@follow',
            function ($user){})->where('user', '[0-9]+');
        Route::get('users/{user}/unFollow', 'SocialMediaController@unFollow',
            function ($user){})->where('user', '[0-9]+');
        Route::get('tweet/{tweet}/likeUnlike', 'LikeController@likeUnlike',
            function ($tweet){})->where('tweet', '[0-9]+');
        Route::get('tweet/{tweet}/likedMe', 'LikeController@likedMe',
            function ($tweet){})->where('tweet', '[0-9]+');
        Route::post('tweet/{tweet}/comments', 'CommentController@store',
            function ($tweet){})->where('tweet', '[0-9]+');
        Route::delete('tweet/delete/{comment}', 'CommentController@destroy',
            function ($comment){})->where('comment', '[0-9]+');
        Route::get('timeline', 'SocialMediaController@timeline');
    });
