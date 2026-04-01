<?php

namespace App\Mail; 

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;





class HourLogsApproved extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(
        public User   $student,
        public int    $approvedCount,
        public float  $totalApprovedHours
    ) {}


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->approvedCount . ' Hour Log(s) Approved',
        );
    }


    public function content(): Content
    {
        return new Content(
            markdown: 'emails.hourlogs.approved',
            with: [
                'studentName'        => $this->student->name,
                'approvedCount'      => $this->approvedCount,
                'totalApprovedHours' => number_format($this->totalApprovedHours, 1),
                'logsUrl'            => url('/student/hours'),
            ],
        );
    }
}

