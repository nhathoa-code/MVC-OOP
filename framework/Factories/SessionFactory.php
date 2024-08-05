<?php

namespace NhatHoa\Framework\Factories;

use NhatHoa\Framework\Base;
use NhatHoa\Framework\Registry;
use NhatHoa\Framework\Session\Drivers;

class SessionFactory extends Base
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
            $parsed = $config->parse(APP_PATH . "/config/session");
            if (!empty($parsed->session->default) && !empty($parsed->session->default->type)){
                $type =$parsed->session->default->type;
                unset($parsed->session->default->type);
                $this->__construct(array(
                    "type" => $type,
                    "options" => (array) $parsed->session->default
                ));
            }
        }
        if (!$this->_type){
            throw new \Exception("Invalid type");
        }
        switch ($this->_type){
            case "server":
                return new Drivers\Server($this->_options);
                break;
            default:
                throw new \Exception("Invalid type");
        }
    }
}
