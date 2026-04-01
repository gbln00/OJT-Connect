<?php


namespace App\Mail;


use App\Models\WeeklyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class ReportReturned extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(
        public WeeklyReport $report
    ) {}


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Week ' . $this->report->week_number . ' Report Needs Revision',
        );
    }


    public function content(): Content
    {
        return new Content(
            markdown: 'emails.report.returned',
            with: [
                'studentName' => $this->report->student->name,
                'weekNumber'  => $this->report->week_number,
                'feedback'    => $this->report->feedback,
                'reportsUrl'  => url('/student/reports'),
            ],
        );
    }
}

