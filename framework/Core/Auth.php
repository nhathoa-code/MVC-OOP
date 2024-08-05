<?php

namespace NhatHoa\Framework\Core;
use NhatHoa\Framework\Base;
use NhatHoa\App\Models\User;
use NhatHoa\Framework\Registry;

class Auth extends Base
{
    protected $_session;
    protected $_guard;

    public function __construct()
    {   
        $this->_session = Registry::get("session");
    }

    public function login(User $user, $guard = "user")
    {
        $this->_session->set("auth=>{$guard}",$user);
    }

    public function user($guard = "user")
    {
        if($this->_session->has("auth=>{$guard}")){
            return $this->_session->get("auth=>{$guard}");
        }
        return null;
    }

    public function logout($guard = "user")
    {
        $this->_session->remove("auth=>{$guard}");
    }

    public function check($guard = "user")
    {
        return $this->_session->has("auth=>{$guard}");
    }
}