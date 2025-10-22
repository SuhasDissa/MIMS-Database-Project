<?php

namespace App\Notifications;

class TransferNotification extends TransactionNotification
{
    public static function createPayload($fromAccount, $toAccount, float $amount, float $fromNewBalance, float $toNewBalance, ?string $description = null, ?string $name = null)
    {
        $subject = "Transfer of Rs. " . number_format($amount, 2) . " completed";
        $lines = [
            ($name ? "Dear {$name}," : 'Dear Customer,'),
            "A transfer of Rs. " . number_format($amount, 2) . " was completed.",
            "From account: {$fromAccount}",
            "To account: {$toAccount}",
            "Your new balance: Rs. " . number_format($fromNewBalance, 2) . ".",
            "Recipient new balance: Rs. " . number_format($toNewBalance, 2) . ".",
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
