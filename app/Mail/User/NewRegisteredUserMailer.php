<?php

namespace App\Mail\User;

use App\Mail\BaseMailer;

class NewRegisteredUserMailer extends BaseMailer
{
    const SUBJECT = 'Welcome to PesoForward';
    const HEADER = 'PesoForward - Registration Confirmation Link';
    const MESSAGE = 'Thank you for registering to PesoForward. To complete your Registration, please click on the link below:';
    const LINK_TEXT = 'Verify Email';

    public function __construct($data)
    {
        $this->emailSubject = self::SUBJECT;
        $this->header = self::HEADER;
        $this->message = self::MESSAGE;
        $this->link = env('UI_APP_URL') . '/verification/' . $data->verifyUser->token;
        $this->linkText = self::LINK_TEXT;
        $this->template = self::TEMPLATE;
        $this->data = $data;
    }
}
