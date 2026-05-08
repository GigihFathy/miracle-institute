<?php

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CertificateIssuedMail extends BaseLearningMail
{
    public function __construct(public Certificate $certificate)
    {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Sertifikat siap diunduh: ' . $this->certificate->certificate_number);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.learning.certificate-issued',
            with: [
                'certificate' => $this->certificate,
                'course' => $this->certificate->course,
                'url' => route('certificates.download', $this->certificate->id),
            ],
        );
    }
}