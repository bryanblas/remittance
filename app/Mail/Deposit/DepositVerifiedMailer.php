<?php

namespace App\Mail\Deposit;

use App\Mail\BaseMailer;

class DepositVerifiedMailer extends BaseMailer
{
    const SUBJECT = 'PesoForward - Deposit';
    const HEADER = 'Deposit to PesoForward';
    const LINK_TEXT = 'Deposit Lists';

    public function __construct($data)
    {
        $this->emailSubject = self::SUBJECT;
        $this->header = self::HEADER;
        $this->message = 'Your deposit with Transaction Number: <b>' . $data->transaction_number . '</b> is now completed.<br/>You can check it on your account:';

        $this->linkText = self::LINK_TEXT;
        $link = env('UI_APP_URL') . '/deposits';

        parent::__construct($link);
    }
}
