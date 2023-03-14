<?php

namespace App\Exceptions;

class UnauthorisedException extends \Exception
{
    protected $message = 'Unauthorised';

    protected $code = 401;
}
