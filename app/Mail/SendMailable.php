<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $emailBody;
    public $emailSubject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailBody, $emailSubject, $emailAttachment)
    {
        //
        $this->emailBody = $emailBody;
        $this->emailSubject = $emailSubject;
		$this->emailAttachment = $emailAttachment; 
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$mail = $this->view('emails.emailtemplate',['emailBody'=>$this->emailBody])->subject($this->emailSubject);
		
		if(!is_null($this->emailAttachment)) {
            $mail->attach($this->emailAttachment['uploadFile'], ['as' => $this->emailAttachment['fileName'], 'mime' => $this->emailAttachment['mimeType']]);
        }
		return $mail;
    }
}
