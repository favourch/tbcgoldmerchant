<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMemberConfirmedTransaction extends Mailable
{
    use Queueable, SerializesModels;

    protected $fullname;
    protected $email;

    public function __construct($fullname, $email)
    {
        $this->fullname = $fullname;
        $this->email = $email;
    }

    public function build()
    {
        return $this->subject('Membership Confirmed')
                    ->from('support@tbcgoldmerchant.org')
                    ->view('emails.send-member-confirmed-transaction')
                        ->with('fullname', $this->fullname)
                        ->with('email', $this->email);
    }
}
