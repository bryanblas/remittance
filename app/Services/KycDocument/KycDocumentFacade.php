<?php

namespace App\Services\KycDocument;

use \Illuminate\Support\Facades\Facade;

class KycDocumentFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\KycDocument\KycDocumentService';
    }
}
