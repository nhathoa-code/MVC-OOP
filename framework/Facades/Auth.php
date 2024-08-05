<?php

namespace NhatHoa\Framework\Facades;
use NhatHoa\Framework\Core\Auth as AuthObject;
use NhatHoa\App\Models\User;

class Auth
{
    protected static $_auth;

    private static function getAuthObject()
    {
        if(!self::$_auth){
            self::$_auth = new AuthObject;
        }
        return self::$_auth;
    }

    public static function login(User $user,$guard = "user")
    {
        self::getAuthObject()->login($user,$guard);
    }

    public static function check($guard = "user")
    {
        return self::getAuthObject()->check($guard);
    }

    public static function user($guard = "user")
    {
        return self::getAuthObject()->user($guard);
    }

    public static function logout($guard = "user")
    {
        self::getAuthObject()->logout($guard);
    }
}