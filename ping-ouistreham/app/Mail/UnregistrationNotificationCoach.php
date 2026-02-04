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
    public $coach; // Ajout du coach

    public function __construct($registration, $coach = null)
    {
        $this->registration = $registration;
        $this->coach = $coach;
    }

    public function build()
    {
        return $this->subject('Désinscription : ' . $this->registration->player_firstname . ' ' . $this->registration->player_lastname)
                    ->view('emails.unregistration_notification');
    }
}