<?php

namespace App\Repositories\Passport;

interface PassportInterface
{
    public function getToken($request);
}
