<?php

namespace NadzorServera\Skijasi\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserVerification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->user = $data['user'];
        $this->token = $data['token'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Potvrda Registracije | HZUTS')
            ->markdown('skijasi::mail.email-verification');
    }
}
