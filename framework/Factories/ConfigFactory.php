<?php

namespace NhatHoa\Framework\Factories;

use NhatHoa\Framework\Base;
use NhatHoa\Framework\Config\Drivers\Ini;

class ConfigFactory extends Base
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
        if (!$this->_type){
            throw new \Exception("Invalid type");
        }
        switch ($this->_type){
            case "ini":
                return new Ini($this->_options);
                break;
            default:
                throw new \Exception("Invalid type");
        }
    }
}
