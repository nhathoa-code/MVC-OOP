<?php

namespace NhatHoa\Framework\Core;
use NhatHoa\Framework\Base;
use NhatHoa\Framework\Registry;

class Response extends Base
{
    protected $_redirectUrl = null;
    protected $_hasFlashMessage = false; 
    protected $_flashMessages = array();
    protected $_status = 302;
    protected $_session;

    public function __construct()
    {
        $this->_session = Registry::get("session");
    }

    public function send($content, $status = 200 ) 
    {
        header("Content-Type: text/html");
        http_response_code($status);
        return $content;
    }

    public function json($content, $status = 200)
    {
        if ($this->_hasFlashMessage) {
            $this->_session->set("message",$this->_flashMessages);
        }
        header("Content-Type: application/json");
        http_response_code($status);
        die(json_encode($content));
    }

    public function back($fallback = '/', $status = 302)
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->_redirectUrl = $_SERVER['HTTP_REFERER'];
        }else{
            $this->_redirectUrl = $fallback;
        }
        $this->_status = $status;
        return $this;
    }

    public function backNow($fallback = '/', $status = 302)
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->_redirectUrl = $_SERVER['HTTP_REFERER'];
        }else{
            $this->_redirectUrl = $fallback;
        }
        $this->_status = $status;
        $this->_execute();
    }

    public function redirect($path)
    {
        $this->_redirectUrl = url($path);
        return $this;
    }

    public function with($key, $message)
    {
        $this->_flashMessages[$key] = $message;
        $this->_hasFlashMessage = true;
        $this->_execute();
    }

    public function flash($name,$message)
    {
        $this->_flashMessages[$name] = $message;
        $this->_hasFlashMessage = true;
        return $this;
    }

    protected function _execute()
    {
        if ($this->_hasFlashMessage) {
            $this->_session->set("message",$this->_flashMessages);
        }
        if ($this->_redirectUrl) {
            header("Location: " . $this->_redirectUrl, true, $this->_status);
            exit;
        }
    }

    public function __destruct()
    {
        $this->_execute();
    }
}