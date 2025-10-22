<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportedUserNotification extends Notification
{
    use Queueable;

    protected $comment;
    protected $status;

    public function __construct($comment, $status)
    {
        $this->comment = $comment;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Comment Has Been Reviewed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("One of your comments has been reported and reviewed.")
            ->line("**Status:** {$this->status}")
            ->line("**Comment Preview:** \"{$this->comment->comment_text}\"")
            ->action('View Comment', url(route('public.artikel_show', $this->comment->article_id) . '#comment-' . $this->comment->comment_id))
            ->line('Please keep our community respectful and constructive.');
    }

    public function toArray($notifiable)
    {
        return [
            'comment_text' => $this->comment->comment_text,
            'status' => $this->status,
            'article_id' => $this->comment->article_id,
        ];
    }
}
