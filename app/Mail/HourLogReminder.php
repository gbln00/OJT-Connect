<?php

namespace App\Mail;

use App\Models\OjtApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HourLogReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User           $student,
        public OjtApplication $application,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Log your OJT hours for today',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.hourlogs.reminder',
            with: [
                'studentName'    => $this->student->name,
                'companyName'    => $this->application->company->name,
                'approvedHours'  => number_format(
                    \App\Models\HourLog::where('application_id', $this->application->id)
                        ->where('status', 'approved')->sum('total_hours'), 1
                ),
                'requiredHours'  => number_format($this->application->required_hours),
                'logsUrl'        => url('/student/hours'),
            ],
        );
    }
}