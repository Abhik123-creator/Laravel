<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewFormSubmission extends Mailable
{
    use Queueable, SerializesModels;

    public $formName;
    public $data;

    public function __construct($formName, $data)
    {
        $this->formName = $formName;
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('New Form Submission: ' . $this->formName)
            ->view('emails.new-form-submission');
    }
}
