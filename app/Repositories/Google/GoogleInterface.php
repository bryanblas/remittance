<?php

namespace App\Repositories\Google;

interface GoogleInterface
{
    public function validateReCaptcha($value);
}
