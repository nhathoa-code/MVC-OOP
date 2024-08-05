<?php

namespace NhatHoa\Framework;

class Event
{
    private static $_callbacks = array();

    private function __construct()
    {
        // do nothing
    }

    private function __clone()
    {
        // do nothing
    }
    
    public static function listen($name, $callback)
    {
        if (empty(self::$_callbacks[$name])){
            self::$_callbacks[$name] = array();
        }
        self::$_callbacks[$name][] = $callback;
    }
    
    public static function dispatch($name, $parameters = null)
    {
        if (!empty(self::$_callbacks[$name])){
            foreach (self::$_callbacks[$name] as $callback){
                call_user_func_array($callback, $parameters);
            }
        }
    }

    public static function remove($name, $callback)
    {
        if (!empty(self::$_callbacks[$name])){
            foreach (self::$_callbacks[$name] as $i => $found){
                if ($callback == $found){
                    unset(self::$_callbacks[$name][$i]);
                }
            }
        }
    }
}
