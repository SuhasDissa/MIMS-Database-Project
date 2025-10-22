<?php

namespace App\Notifications;

class DepositNotification extends TransactionNotification
{
    public function __construct(string $subject, array $lines, ?string $name = null)
    {
        parent::__construct($subject, $lines, $name);
    }

    public static function createPayload($accountNumber, float $amount, float $newBalance, ?string $description = null, ?string $name = null)
    {
        $subject = "Deposit of Rs. " . number_format($amount, 2) . " credited to your account";
        $lines = [
            ($name ? "Dear {$name}," : 'Dear Customer,'),
            "We have received a deposit of Rs. " . number_format($amount, 2) . " to your account {$accountNumber}.",
            "New balance: Rs. " . number_format($newBalance, 2) . ".",
        ];

        if ($description) {
            $lines[] = "Description: {$description}";
        }

        $lines[] = "If you did not authorize this transaction, please contact us immediately.";

        return [
            'subject' => $subject,
            'lines' => $lines,
            'name' => $name,
        ];
    }
}
