<?php

namespace App\Mail\KycDocument;

use App\Mail\BaseMailer;

class DocumentSubmissionMailer extends BaseMailer
{
    const SUBJECT = 'PesoForward - KYC Documents';
    const HEADER = 'KYC Documents Submitted';
    const LINK_TEXT = 'Check KYC Documents';

    public function __construct($data)
    {
        $this->emailSubject = self::SUBJECT;
        $this->header = self::HEADER;
        $this->message = 'You have submitted your KYC Documents [' . $data->document_type . '], please give us time to review your documents. To check updates for the status of your documents, please check the link below.';
        $this->linkText = self::LINK_TEXT;
        $link = env('UI_APP_URL') . '/kyc';

        parent::__construct($link);
    }
}
