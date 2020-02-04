<?php

namespace App\Mail\KycDocument;

use App\Mail\BaseMailer;

class DocumentUpdateMailer extends BaseMailer
{
    const SUBJECT = 'PesoForward - KYC Documents Update';
    const HEADER = 'KYC Documents Updated';
    const LINK_TEXT = 'Check KYC Documents';

    public function __construct($data)
    {
        $this->emailSubject = self::SUBJECT;
        $this->header = self::HEADER;
        $this->message = 'The document you submitted [' . $data->document_type . '] is now Reviewed by one of our checker.';

        if ($data->status == 'Verified') {
            $status = '<br/><br/>Current Status: <b class="text_green">' . $data->status . '</b>';
        } else {
            $status = '<br/>Current Status: <b class="text_red">' . $data->status . '</b>';
            $status .= '<br/>Reason: ' . $data->remarks;
        }

        $this->message .= $status . '<br/><br/>You can check the details of you KYC documents here:';
        $this->linkText = self::LINK_TEXT;
        $link = env('UI_APP_URL') . '/kyc';

        parent::__construct($link);
    }
}
