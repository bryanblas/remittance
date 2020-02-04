<?php

namespace App\Mail\User;

use App\Mail\BaseMailer;

class VerificationUserMailer extends BaseMailer
{
    const SUBJECT = 'Successful Verification';
    const HEADER = 'PesoForward - Successful Verification';
    const MESSAGE = 'You have successfully verified your account. To login, please click on the link below:';
    const LINK_TEXT = 'Login';

    public function __construct($data)
    {
        $this->emailSubject = self::SUBJECT;
        $this->header = self::HEADER;
        $this->message = self::MESSAGE;
        $this->link = env('UI_APP_URL') . '/login';
        $this->linkText = self::LINK_TEXT;
        $this->template = self::TEMPLATE;
        $this->data = $data;
    }
}
