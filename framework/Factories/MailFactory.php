<?php

namespace NhatHoa\Framework\Factories;

use NhatHoa\Framework\Base;
use NhatHoa\Framework\Registry;
use NhatHoa\Framework\Mail\Drivers;

class MailFactory extends Base
{
    /**
    * @readwrite
    */
    protected $_type;
    /**
    * @readwrite
    */
    protected $_options;
    protected function _getExceptionForImplementation($method)
    {
        return new \Exception("{$method} method not implemented");
    }
    public function initialize()
    {
        $config = Registry::get("config");
        if($config){
            $parsed = $config->parse(APP_PATH . "/config/mail");
            if (!empty($parsed->mail->default) && !empty($parsed->mail->default->type)){
                $type =$parsed->mail->default->type;
                unset($parsed->mail->default->type);
                $this->__construct(array(
                    "type" => $type,
                    "options" => (array) $parsed->mail->default
                ));
            }
        }
        if (!$this->_type){
            throw new \Exception("Invalid type");
        }
        switch ($this->_type){
            case "smtp":
                return new Drivers\SMTP($this->_options);
                break;
            default:
                throw new \Exception("Invalid type");
        }
    }
}
