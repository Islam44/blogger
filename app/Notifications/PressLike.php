<?php

namespace App\Notifications;

use App\Tweet;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PressLike extends Notification
{
    use Queueable;
    protected $user;
    protected $tweet;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user,Tweet $tweet)
    {
        $this->user=$user;
        $this->tweet=$tweet;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->id,
            'read_at' => null,
            'data' => [
                'user_id' => $this->user->id,
                'user_name' => $this->user->name,
                'tweet_id' => $this->tweet->id,
                'tweet_text' => $this->tweet->text,
            ],
        ];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'data' => [
                'user_id' => $this->user->id,
                'user_name' => $this->user->name,
                'tweet_id' => $this->tweet->id,
                'tweet_text' => $this->tweet->text,

            ],
        ];
    }
}
