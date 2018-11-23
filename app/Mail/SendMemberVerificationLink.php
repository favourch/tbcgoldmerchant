<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMemberVerificationLink extends Mailable
{
    use Queueable, SerializesModels;

    protected $fullname;
    protected $email;

    public function __construct($fullname, $email)
    {
        $this->fullname = $fullname;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.send-member-verification-link')
                    ->with('fullname', $this->fullname)
                    ->with('email', $this->email);
    }
}
