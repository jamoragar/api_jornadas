<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnviarBono extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject('Bono(s) de Sorteo')
                    ->markdown('aporteBono');

        if(!empty($this->data["attachments"])){
            foreach($this->data["attachments"] as $k => $v){
                $mail = $mail->attach($v["path"], [
                    'as' => $v["as"],
                    'mime' => $v["mime"],
                ]);
            }
        }
    }
}
