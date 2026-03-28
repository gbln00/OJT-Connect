<?php

namespace App\Mail;

use App\Models\TenantRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantApproved extends Mailable
{
    use Queueable, SerializesModels;

    public TenantRegistration $registration;

    public function __construct(TenantRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function build(): self
    {
        return $this->subject('Your Account Has Been Approved!')
                    ->view('emails.tenant_approved');
    }
}