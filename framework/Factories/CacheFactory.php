<?php

namespace NhatHoa\Framework\Factories;

use NhatHoa\Framework\Base;
use NhatHoa\Framework\Registry;
use NhatHoa\Framework\Cache\Drivers;

class CacheFactory extends Base
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
       // return new Exception\Implementation("{$method} method not implemented");
    }
    public function initialize()
    {
        $config = Registry::get("config");
        if($config){
            $parsed = $config->parse(APP_PATH . "/config/cache");
            if (!empty($parsed->cache->default) && !empty($parsed->cache->default->type)){
                $type =$parsed->cache->default->type;
                unset($parsed->cache->default->type);
                $this->__construct(array(
                    "type" => $type,
                    "options" => (array) $parsed->cache->default
                ));
            }
        }
        if (!$this->_type){
            throw new \Exception("Invalid type");
        }
        switch ($this->_type){
            case "redis":
                return new Drivers\Redis($this->_options);
                break;
            default:
                throw new \Exception("Invalid type");
        }
    }
}