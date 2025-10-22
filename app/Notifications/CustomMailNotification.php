<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomMailNotification extends Notification
{
    use Queueable;

    protected string $subject;
    protected string $body;
    protected ?string $name;

    /**
     * Create a new notification instance.
     *
     * @param string $subject
     * @param string $body
     * @param string|null $name
     */
    public function __construct(string $subject, string $body, ?string $name = null)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->name = $name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->subject);

        if ($this->name) {
            $mail->greeting("Hello {$this->name},");
        }

        // Allow body to contain multiple lines separated by "\n"
        $lines = explode("\n", $this->body);
        foreach ($lines as $line) {
            $mail->line($line);
        }

        $mail->salutation('Regards,\nMIMS Team');

        return $mail;
    }
}
