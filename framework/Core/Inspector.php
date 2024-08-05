<?php

namespace NhatHoa\Framework\Core;
use NhatHoa\Framework\Utilities\StringMethods;
use NhatHoa\Framework\Utilities\ArrayMethods;

class Inspector
{
    protected $_class;

    protected $_meta = array(
        "class" => array(),
        "properties" => array(),
        "methods" => array()
    );

    protected $_properties = array();

    protected $_methods = array();

    public function __construct($class)
    {
        $this->_class = $class;
    }

    protected function _getClassComment()
    {
        $reflection = new \ReflectionClass($this->_class);
        return $reflection->getDocComment();
    }

    protected function _getClassProperties()
    {
        $reflection = new \ReflectionClass($this->_class);
        return $reflection->getProperties();
    }

    protected function _getClassMethods()
    {
        $reflection = new \ReflectionClass($this->_class);
        return $reflection->getMethods();
    }

    protected function _getPropertyComment($property)
    {
        $reflection = new \ReflectionProperty($this->_class, $property);
        return $reflection->getDocComment();
    }

    protected function _getMethodComment($method)
    {
        $reflection = new \ReflectionMethod($this->_class, $method);
        return $reflection->getDocComment();
    }
    
    protected function _parse($comment)
    {
        $meta = array();
        $pattern = "(@[a-zA-Z]+\s*[a-zA-Z0-9, ()_]*)";
        $matches = StringMethods::match($comment, $pattern);
        if ($matches != null){
            foreach ($matches as $match){
                $parts = ArrayMethods::clean(
                    ArrayMethods::trim(
                        StringMethods::split($match, "[\s]", 2)
                    )
                );
                $meta[$parts[0]] = true;
                if (sizeof($parts) > 1){
                    $meta[$parts[0]] = ArrayMethods::clean(
                        ArrayMethods::trim(
                            StringMethods::split($parts[1], ",")
                        )
                    );
                }
            }
        }
        return $meta;
    }

    public function getClassProperties()
    {
        if (!isset($this->_properties)){
            $properties = $this->_getClassProperties();
            foreach ($properties as $property){
                $this->_properties[] = $property->getName();
            }
        }
        return $this->_properties;
    }

    public function getClassMethods()
    {
        if (!isset($this->_methods)){
            $methods = $this->_getClassMethods();
            foreach ($methods as $method){
                $this->_methods[] = $method->getName();
            }
        }
        return $this->_properties;
    }

    public function getClassMeta()
    {
        if (!isset($this->_meta["class"])){
            $comment = $this->_getClassComment();
            if (!empty($comment)){
                $this->_meta["class"] = $this->_parse($comment);
            }
            else{
                $this->_meta["class"] = null;
            }
        }
        return $this->_meta["class"];
    }

    public function getPropertyMeta($property)
    {
        if (!isset($this->_meta["properties"][$property])){
            $comment = $this->_getPropertyComment($property);
            if (!empty($comment)){
                $this->_meta["properties"][$property] = $this->_parse($comment);
            }else{
                $this->_meta["properties"][$property] = null;
            }
        }
        return $this->_meta["properties"][$property];
    }

    public function getMethodMeta($method)
    {
        if (!isset($this->_meta["actions"][$method])){
            $comment = $this->_getMethodComment($method);
            if (!empty($comment)){
                $this->_meta["methods"][$method] = $this->_parse($comment);
            }else{
                $this->_meta["methods"][$method] = null;
            }
        }
        return $this->_meta["methods"][$method];
    }

}