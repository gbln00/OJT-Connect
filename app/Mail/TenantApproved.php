<?php

namespace App\Mail;

use App\Models\TenantRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TenantRegistration $registration,
        public string $plainPassword,
    ) {}

    public function build(): self
    {
        return $this->subject('Your Account Has Been Approved!')
                    ->view('emails.tenant_approved');
    }
}