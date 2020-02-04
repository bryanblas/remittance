<?php

namespace App\Mail\User;

use App\Mail\BaseMailer;

class ForgotPasswordMailer extends BaseMailer
{
    const SUBJECT = 'AndexPay - Reset Merchant Password';
    const HEADER = 'Reset Your AndexPay - Merchant Password';
    const MESSAGE = 'We\'ve received your request to reset your AndexPay - Merchant account password. To continue, please follow the link below.!';
    const LINK_TEXT = 'Reset Password';

    public function __construct($link)
    {
        $this->emailSubject = self::SUBJECT;
        $this->header = self::HEADER;
        $this->message = self::MESSAGE;
        $this->linkText = self::LINK_TEXT;

        parent::__construct($link);
    }
}
