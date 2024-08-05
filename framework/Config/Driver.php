<?php

namespace NhatHoa\Framework\Config;

class Driver
{
    protected $_parsed = array();
    public function initialize()
    {
        return $this;
    }
    protected function _getExceptionForImplementation($method)
    {
        return new \Exception("{$method} method not implemented");
    }
}