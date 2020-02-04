<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BaseMailer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    const TEMPLATE = 'mail.generic';

    protected $template;
    protected $emailSubject;
    public $header;
    public $message;
    public $link;
    public $linkText;
    public $data;

    public function __construct($link)
    {
        $this->link = $link;
        $this->template = self::TEMPLATE;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject($this->emailSubject)->markdown($this->template);
    }
}
