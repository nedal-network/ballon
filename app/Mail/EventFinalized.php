<?php

namespace App\Mail;

use App\Models\AircraftLocationPilot;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class EventFinalized extends Mailable
{
    use Queueable, SerializesModels;

    public string $confirmationLink;
    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public Coupon $coupon,
        public AircraftLocationPilot $event,
    ) 
    {
        $this->confirmationLink = URL::signedRoute('event-confirmation', [
            'coupon_id' => $this->coupon->id,
            'event_id' => $this->event->id,
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Véglegesített repülés',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.event-finalized',
        );
    }
}
