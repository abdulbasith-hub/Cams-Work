<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SecurePdfMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdfPath;

    public function __construct($pdfPath)
    {
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->view('emails.secure_pdf')
            ->subject('Here is your password-protected PDF')
            ->attach($this->pdfPath, [
                'as' => 'MySecurePDF.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
