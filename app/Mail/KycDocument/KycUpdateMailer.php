<?php

namespace App\Mail\KycDocument;

use App\Mail\BaseMailer;

class KycUpdateMailer extends BaseMailer
{
    const SUBJECT = 'PesoForward - KYC Status Update';
    const HEADER = 'KYC Status Updated';
    const LINK_TEXT = 'Check KYC Status';

    public function __construct($data)
    {
        $this->emailSubject = self::SUBJECT;
        $this->header = self::HEADER;

        if ($data->kyc_status == 'Verified') {
            $this->message = 'Congratulations! Your KYC Status are now <span class="text_green">Verified</span> and you may now start your transactions.';
        } else {
            $this->message = 'Sorry, but your account has been Rejected. Please provide necessary documents or contact our support.';
        }
        $this->linkText = self::LINK_TEXT;
        $link = env('UI_APP_URL') . '/kyc';

        parent::__construct($link);
    }
}
