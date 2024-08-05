<?php

namespace NhatHoa\Framework\Database;

use NhatHoa\Framework\Base;

class Connector extends Base
{
    public function initialize()
    {
        return $this;
    }
    protected function _getExceptionForImplementation($method)
    {
        return new \Exception("{$method} method not implemented");
    }
}