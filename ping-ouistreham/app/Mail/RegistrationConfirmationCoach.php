<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationConfirmationCoach extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $coach;

    public function __construct($registration, $coach = null)
    {
        $this->registration = $registration;
        $this->coach = $coach;
    }

    public function build()
    {
        return $this->subject('Nouvelle Inscription : ' . $this->registration->player_firstname)
                    ->view('emails.registration_confirmation');
    }
}