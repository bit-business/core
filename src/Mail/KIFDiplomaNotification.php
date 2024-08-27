<?php

namespace NadzorServera\Skijasi\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KIFDiplomaNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

     public function build()
    {
        return $this
            ->markdown('skijasi::mail.email-kifdiploma')
            ->subject('Obavijest za KIF studente');
    }
   
}
