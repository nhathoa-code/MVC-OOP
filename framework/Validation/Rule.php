<?php

namespace NhatHoa\Framework\Validation;

use NhatHoa\Framework\Validation\Rules\Unique;
use NhatHoa\Framework\Validation\Rules\Exists;

class Rule
{
    public static function __callStatic($name, $arguments)
    {
        switch($name)
        {
            case "unique":
                return new Unique($arguments);
                break;
            case "exists":
                return new Exists($arguments);
                break;
            default:
                throw new \Exception("rule not found");
        }
    }
}