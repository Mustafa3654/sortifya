<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The contact form notification.
 *
 * The From address is always the platform's own verified sender, never the
 * visitor's. Relays authenticate the sender domain — put a stranger's gmail
 * in From and Brevo either rejects the message outright or it lands in spam
 * for failing SPF/DKIM.
 *
 * Reply-To carries the visitor instead, so hitting reply in your mail client
 * answers them directly.
 */
class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        $subject = str($this->contactMessage->subject)->squish()->limit(70)->toString();

        return new Envelope(
            subject: "Sortifya contact — {$subject}",
            replyTo: [new Address($this->contactMessage->email, $this->contactMessage->name)],
            // Threads a conversation in most clients and makes the sender
            // obvious in a crowded inbox.
            tags: ['contact-form'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-message',
            with: ['contact' => $this->contactMessage],
        );
    }
}
