<?php

namespace NhatHoa\Framework\Facades;
use NhatHoa\Framework\Registry;

class Gate
{
    public static function __callStatic($name, $arguments)
    {
        switch($name){
            case "allows":
                $auth = Registry::get("authorization");
                $name = $arguments[0];
                unset($arguments[0]);
                return $auth->allows($name,$arguments);
                break;
            default :
                throw new \Exception("method not found");    
        }
    }
}