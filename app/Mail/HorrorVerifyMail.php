<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HorrorVerifyMail extends Mailable {
    use Queueable, SerializesModels;

    public $content;
    public $actionUrl;
    public $actionText;

    public function __construct( $content, $actionUrl = null, $actionText = 'Verify Email Address' ) {
        $this->content = $content;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    public function build() {
        return $this->subject( 'Verify Your Existence' )
        ->view( 'emails.horror_verify' )
        ->with( [
            'content' => $this->content,
            'actionUrl' => $this->actionUrl,
            'actionText' => $this->actionText,
        ] );
    }
}
