<?php


namespace App\Mail;


use App\Models\Evaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class EvaluationSubmitted extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(
        public Evaluation $evaluation
    ) {}


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your OJT Evaluation Has Been Submitted',
        );
    }


    public function content(): Content
    {
        return new Content(
            markdown: 'emails.evaluation.submitted',
            with: [
                'studentName'    => $this->evaluation->student->name,
                'companyName'    => $this->evaluation->application->company->name,
                'overallGrade'   => $this->evaluation->overall_grade,
                'recommendation' => ucfirst($this->evaluation->recommendation),
                'ratingLabel'    => $this->evaluation->rating_label,
                'dashboardUrl'   => url('/student/evaluation'),
            ],
        );
    }
}

