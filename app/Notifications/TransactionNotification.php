<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

abstract class TransactionNotification extends Notification
{
    use Queueable;

    protected string $subject;
    protected array $lines;
    protected ?string $name;

    public function __construct(string $subject, array $lines, ?string $name = null)
    {
        $this->subject = $subject;
        $this->lines = $lines;
        $this->name = $name;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->subject);

        if ($this->name) {
            $mail->greeting("Hello {$this->name},");
        }

        foreach ($this->lines as $line) {
            $mail->line($line);
        }

        $mail->salutation('Regards,\nMIMS Team');

        return $mail;
    }
}
