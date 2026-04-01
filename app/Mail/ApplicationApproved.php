<?php


namespace App\Mail;


use App\Models\OjtApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class ApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(
        public OjtApplication $application
    ) {}


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your OJT Application Has Been Approved',
        );
    }


    public function content(): Content
    {
        return new Content(
            markdown: 'emails.application.approved',
            with: [
                'studentName'   => $this->application->student->name,
                'companyName'   => $this->application->company->name,
                'program'       => $this->application->program,
                'semester'      => $this->application->semester,
                'requiredHours' => $this->application->required_hours,
                'remarks'       => $this->application->remarks,
                'dashboardUrl'  => url('/student/dashboard'),
            ],
        );
    }
}

