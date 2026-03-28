<?php

namespace App\Mail;

use App\Models\TenantRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantRejected extends Mailable
{
    use Queueable, SerializesModels;

    public TenantRegistration $registration;

    public function __construct(TenantRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function build(): self
    {
        return $this->subject('Update on Your Registration')
                    ->view('emails.tenant_rejected');
    }
}