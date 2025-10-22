<?php

namespace App\Notifications;

class WithdrawNotification extends TransactionNotification
{
    public static function createPayload($accountNumber, float $amount, float $newBalance, ?string $description = null, ?string $name = null)
    {
        $subject = "Withdrawal of Rs. " . number_format($amount, 2) . " from your account";
        $lines = [
            ($name ? "Dear {$name}," : 'Dear Customer,'),
            "A withdrawal of Rs. " . number_format($amount, 2) . " has been made from your account {$accountNumber}.",
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
