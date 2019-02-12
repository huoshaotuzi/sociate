<?php

namespace Huoshaotuzi\Sociate\Exception;

use Exception;

class DriverNullException extends Exception
{
    public function __construct($message, $code = 500)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return __class__ . ": [{$this->code}]: {$this->message}\n";
    }
}
