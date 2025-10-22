<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\CustomMailNotification;
use Illuminate\Console\Command;

class SendCustomEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Examples:
     *  php artisan notify:email --to=someone@example.com --subject="Hi" --body="Line1\nLine2"
     *  php artisan notify:email --user=1 --subject="Hello" --body="Your report is ready"
     *
     * @var string
     */
    protected $signature = 'notify:email
                            {--to= : Email address to send to (optional if --user provided)}
                            {--user= : User ID to send notification to (optional if --to provided)}
                            {--subject= : Subject of the email}
                            {--body= : Body of the email (use \n for new lines)}
                            {--name= : Optional name to personalize greeting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a customized email using the CustomMailNotification';

    public function handle()
    {
        $to = $this->option('to');
        $userId = $this->option('user');
        $subject = $this->option('subject') ?? 'Notification from MIMS';
        $body = $this->option('body') ?? '';
        $name = $this->option('name');

        if (!$to && !$userId) {
            $this->error('Provide either --to email or --user id.');
            return 1;
        }

        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with id {$userId} not found.");
                return 1;
            }

            $user->notify(new CustomMailNotification($subject, $body, $name ?? $user->name ?? null));
            $this->info("Notification sent to user id {$userId} ({$user->email}).");
            return 0;
        }

        // Send to arbitrary email address using Notification facade convenience
        // We'll create a simple Notifiable on the fly
        $notifiable = new class {
            use \Illuminate\Notifications\Notifiable;
            public $email;
            public function routeNotificationForMail($notification)
            {
                return $this->email;
            }
        };

        $notifiable->email = $to;

        $notifiable->notify(new CustomMailNotification($subject, $body, $name));

        $this->info("Notification sent to {$to}");
        return 0;
    }
}
