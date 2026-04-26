<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HorrorMail extends Mailable {
    use Queueable, SerializesModels;

    public $subject;
    public $content;
    public $actionUrl;
    public $actionText;
    public $embeddedLogo;

    public function __construct( $subject, $content, $actionUrl = null, $actionText = null ) {
        $this->subject = $subject;
        $this->content = $content;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    public function build() {
        // Embed the logo image from public/images/logo.png
        $this->embeddedLogo = $this->embed( public_path( 'images/logo.png' ) );

        return $this->subject( $this->subject )
        ->view( 'emails.horror' )
        ->with( [
            'subject'    => $this->subject,
            'content'    => $this->content,
            'actionUrl'  => $this->actionUrl,
            'actionText' => $this->actionText,
            'logo'       => $this->embeddedLogo,
        ] );
    }
}
