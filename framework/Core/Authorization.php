<?php

namespace NhatHoa\Framework\Core;

use NhatHoa\Framework\Facades\Auth;

class Authorization implements \Serializable
{
    protected $before;
    protected $authorizations = array();
    protected $guard = "admin";

    public function for(string $name, callable|array $callback)
    {
        $this->authorizations[$name] = $callback;
    }

    public function allows(string $name, array $args = [])
    {
        $user = Auth::user($this->guard);
        if(!$user){
            return false;
        }
        if($this->before){
            $return_before = $this->runCallback($this->before,[$user,...$args]);
            if($return_before){
                return true;
            }
        }
        if(isset($this->authorizations[$name])){
            $callback = $this->authorizations[$name];
            return $this->runCallback($callback,[$user,...$args]);
        }
        return false;
    }

    protected function runCallback(callable|array $callback,array $args)
    {
        $user = Auth::user($this->guard);
        if(!$user){
            return false;
        }
        if(is_callable($callback)){
            $return = call_user_func_array(
                $callback,
                [$user,...$args]
            );
        }elseif(is_array($callback)){
            if(count($callback) < 2){
                throw new \Exception("Invalid class's method");
            }
            list($authClass,$method) = $callback;
            $authObj = new $authClass();
            if(method_exists($authObj,$method)){
                $return = call_user_func_array([$authObj,$method],[$user,...$args]);    
            }else{
                throw new \Exception("Invalid class's method");
            }
        }
        if($return == null){
            return false;
        }else{
            return $return;
        }
    }

    public function before(callable|array $callback)
    {
       $this->before = $callback;
    }

    public function forGuard(string $name = "")
    {
        if(!empty($name)){
            $this->guard = $name;
        }
    }

    public function serialize()
    {
        return serialize([
            'authorizations' => $this->authorizations,
            'guard' => $this->guard,
            'before' => $this->before,
        ]);
    }

    public function unserialize(string $data)
    {
        $data = unserialize($data);
        $this->authorizations = $data['authorizations'];
        $this->guard = $data['guard'];
        $this->before = $data['before'];
    }
}