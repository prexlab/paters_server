<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Simple extends Mailable
{
    use Queueable, SerializesModels;

    public $options;
    public $data;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($options)
    {
        $this->options = $options + ['template'=>'emails.simple'];
        $this->data = $options;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->options['to'])
            ->from($this->options['from'], $this->options['from_jp'])
            ->subject($this->options['subject'])
            ->text($this->options['template']);
    }
}
