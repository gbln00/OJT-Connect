<?php

namespace App\Mail;

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
        public User  $student,
        public int   $pendingDays,
        public float $totalApprovedHours,
        public float $requiredHours,
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
                'studentName'         => $this->student->name,
                'pendingDays'         => $this->pendingDays,
                'totalApprovedHours'  => number_format($this->totalApprovedHours, 1),
                'requiredHours'       => number_format($this->requiredHours, 1),
                'logsUrl'             => url('/student/hours'),
            ],
        );
    }
}