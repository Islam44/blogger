<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\Tweet;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','image',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tweets()
    {
        return $this->hasMany(Tweet::class,'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->belongsToMany(Tweet::class,
            'likes',
            'user_id',
            'tweet_id');
    }

    public function follows()
    {
        return $this->belongsToMany(self::class,
            'follower',
            'user_id',
            'follows_id')
            ->withTimestamps();
    }

    public function follow($user_id)
    {
        $this->follows()->attach($user_id);
        return $this;
    }

    public function un_follow($user_id)
    {
        $this->follows()->detach($user_id);
        return $this;
    }
    public function isFollowing($user_id)
    {
        $check=(boolean)$this->follows()->where('follows_id',$user_id)->first(["users.id"]);
        return $check;
    }
    public function isLiking($tweet_id)
    {
        return Like::where('tweet_id','=',$tweet_id)->where('user_id','=',auth()->user()->id)->first();
    }
}
