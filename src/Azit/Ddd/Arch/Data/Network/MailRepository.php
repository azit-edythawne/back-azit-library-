<?php

namespace Azit\Ddd\Arch\Data\Network;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailRepository extends Mailable {
    use Queueable, SerializesModels;

    protected string $configView;
    protected string $configSubject;
    protected array $configData;

    /**
     * Create a new message instance.
     * @return void
     */
    public function __construct(string $view, string $subject, array $data = []) {
        $this -> configView = $view;
        $this -> configSubject = $subject;
        $this -> configData = $data;
    }


    /**
     * Get the message envelope.
     * @return Envelope
     */
    public function envelope() {
        return new Envelope(subject : $this->configSubject);
    }

    /**
     * Get the message content definition.
     * @return Content
     */
    public function content() {
        return new Content(view : $this->configView, with: $this->configData);
    }

}