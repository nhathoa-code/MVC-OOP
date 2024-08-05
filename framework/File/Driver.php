<?php

namespace NhatHoa\Framework\File;

use NhatHoa\Framework\Base;

class Driver extends Base
{
    protected $_name;
    protected $_tmp_name;
    protected $_size;
    protected $_error;
    protected $_type;

    public function __construct($name = null,$tmp_name = null,$size = null,$error = null,$type = null) 
    {
        $this->_name = $name;
        $this->_tmp_name = $tmp_name;
        $this->_size = $size;
        $this->_error = $error;
        $this->_type = $type;
    }


    public function initialize()
    {
        return $this;
    }
    
    protected function _getExceptionForImplementation($method)
    {
        return new \Exception("{$method} method not implemented");
    }
}