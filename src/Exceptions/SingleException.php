<?php

namespace NadzorServera\Skijasi\Exceptions;

use Exception;

class SingleException extends Exception
{
    public function __construct($message)
    {
        $this->message = $message;
    }
}
