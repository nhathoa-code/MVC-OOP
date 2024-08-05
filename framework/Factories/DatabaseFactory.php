<?php

namespace NhatHoa\Framework\Factories;
use NhatHoa\Framework\Base;
use NhatHoa\Framework\Registry;
use NhatHoa\Framework\Database\Connectors;

class DatabaseFactory extends Base
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
            $parsed = $config->parse(APP_PATH . "/config/database");
            if (!empty($parsed->database->default) && !empty($parsed->database->default->type)){
                $type =$parsed->database->default->type;
                unset($parsed->database->default->type);
                $this->__construct(array(
                    "type" => $type,
                    "options" => (array) $parsed->database->default
                ));
            }
        }
        if (!$this->_type){
            throw new \Exception("Invalid type");
        }
        switch ($this->_type)
        {
            case "mysql":
                return new Connectors\Mysql($this->_options);
                break;
            default:
                throw new \Exception("Invalid type");
                break;
        }
    }
}
