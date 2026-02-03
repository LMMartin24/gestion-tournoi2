<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UnregistrationNotificationCoach extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    public function build()
    {
        return $this->subject('Désinscription : ' . $this->registration->player_firstname . ' ' . $this->registration->player_lastname)
                    ->view('emails.unregistration_notification_coach');
    }
}