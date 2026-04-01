<?php


namespace App\Mail;


use App\Models\OjtApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class ApplicationRejected extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(
        public OjtApplication $application
    ) {}


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on Your OJT Application',
        );
    }


    public function content(): Content
    {
        return new Content(
            markdown: 'emails.application.rejected',
            with: [
                'studentName'  => $this->application->student->name,
                'companyName'  => $this->application->company->name,
                'remarks'      => $this->application->remarks,
                'applyUrl'     => url('/student/application/create'),
            ],
        );
    }
}

