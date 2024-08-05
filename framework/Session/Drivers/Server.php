<?php

namespace NhatHoa\Framework\Session\Drivers;
use NhatHoa\Framework\Session\Driver;

class Server extends Driver
{
    protected $_flashMessage = [];

    public function __construct()
    {
        session_start();
    }
    
    public function set($key, $value)
    {
        if(strpos($key,"=>") !== false){
            $arrayKeys = explode("=>",$key);
            $currentSession = &$_SESSION;
            $lastIndex = count($arrayKeys) - 1;
            foreach ($arrayKeys as $index => $key) {
                if(isset($currentSession[$key])){
                    $currentSession = &$currentSession[$key];
                    if($lastIndex === $index){
                        $currentSession = $value;
                    }
                }else{
                    if($lastIndex === $index){
                        $currentSession[$key] = $value;
                    }else{
                        $currentSession[$key] = array();
                    }
                    $currentSession = &$currentSession[$key];
                }
            }
        }else{
            $_SESSION[$key] = $value;
        }
    }

    public function get($key, $default = null)
    {
        if(strpos($key,"=>") !== false){
            $arrayKeys = explode("=>",$key);
            $currentSession = &$_SESSION;
            $lastIndex = count($arrayKeys) - 1;
            foreach ($arrayKeys as $index => $key) {
                if(isset($currentSession[$key])){
                    $currentSession = &$currentSession[$key];
                }else{
                    return null;
                }
                if($lastIndex === $index){
                   return $currentSession;
                }
            }
        }
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public function remove($key)
    {
        if(strpos($key,"=>") !== false){
            $arrayKeys = explode("=>",$key);
            $currentSession = &$_SESSION;
            $lastIndex = count($arrayKeys) - 1;
            foreach ($arrayKeys as $index => $key) {
                if(isset($currentSession[$key])){
                    if($lastIndex === $index){
                        unset($currentSession[$key]);
                        return true;
                    }else{
                        $currentSession = &$currentSession[$key];
                    }
                }else{
                    return false;
                }
            }
        }else{
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
                return true;
            }
        }
    }

    public function push($key_str,$value,$key_index = null)
    {
        $arrayKeys = explode("=>",$key_str);
        $currentSession = &$_SESSION;
        $lastIndex = count($arrayKeys) - 1;
        foreach ($arrayKeys as $index => $key) {
            if (!isset($currentSession[$key])) {
                $currentSession[$key] = [];
            }
            if($lastIndex === $index){
                if($key_index){
                    $currentSession[$key][$key_index] = $value;
                }else{
                    $currentSession[$key][] = $value;
                }
            }else{
                $currentSession = &$currentSession[$key];
            }
        }
    }

    public function has($key)
    {
        if(strpos($key,"=>") !== false){
            $arrayKeys = explode("=>",$key);
            $currentSession = &$_SESSION;
            $lastIndex = count($arrayKeys) - 1;
            foreach ($arrayKeys as $index => $key) {
                if(isset($currentSession[$key])){
                    $currentSession = &$currentSession[$key];
                }else{
                    return false;
                }
                if($lastIndex === $index){
                   return isset($currentSession);
                }
            }
        }else{
           return isset($_SESSION[$key]);
        }
    }

    public function all()
    {
        return $_SESSION;
    }

    public function destroy()
    {
        session_destroy();
    }
}