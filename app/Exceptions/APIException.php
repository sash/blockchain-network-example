<?php

namespace App\Exceptions;

use Throwable;

class APIException extends \Exception
{
    function __construct(string $message = "", int $code = 0, $data)
    {
        parent::__construct($message, $code);
        $this->data = $data;
    }
    
    public $data;
}