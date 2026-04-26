<?php

namespace App\Notifications;

use App\Mail\HorrorMail;
use Illuminate\Notifications\Notification;

class CustomVerifyEmail extends Notification
{
    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $subject = 'Verify Your Existence';
        $content = "You have been chosen to enter the abyss. Click the button below to verify your email address and prove your worth.";

        return (new HorrorMail($subject, $content))
                    ->to($notifiable->getEmailForVerification()) // Explicitly set recipient
                    ->with([
                        'actionUrl' => $verificationUrl,
                        'actionText' => 'Verify Email Address',
                        'displayableActionUrl' => $verificationUrl,
                    ]);
    }

    /**
     * Generate the email verification URL.
     */
    protected function verificationUrl($notifiable)
    {
        return url(route('verification.verify', [
            'id'   => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
        ], false));
    }
}
