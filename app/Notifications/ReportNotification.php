<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportNotification extends Notification
{
    use Queueable;

    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'report_id' => $this->report->id,
            'reporter' => $this->report->user->name,
            'comment_excerpt' => substr($this->report->comment->content, 0, 50),
            'reason' => $this->report->reason,
        ];
    }
}