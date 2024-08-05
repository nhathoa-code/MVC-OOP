<?php 

namespace NhatHoa\Framework;
use NhatHoa\Framework\Core\Inspector;
use NhatHoa\Framework\Utilities\StringMethods;

class Base
{
    private $_inspector;

    public function __construct($options = array())
    {
        $this->_inspector = new Inspector($this);
        if (is_array($options) || is_object($options)){
            foreach ($options as $key => $value){
                $key = ucfirst($key);
                $method = "set{$key}";
                $this->$method($value);
            }
        }
    }

    public function __call($name, $arguments)
    {
        if (empty($this->_inspector)){
            throw new \Exception("Method {$name} not found or Call parent::__construct!");
        }
        $getMatches = StringMethods::match($name, "^get([a-zA-Z0-9]+)$");
        if ($getMatches && sizeof($getMatches) > 0){
            $normalized = lcfirst($getMatches[0]);
            $property = "_{$normalized}";
            if (property_exists($this, $property)){
                $meta = $this->_inspector->getPropertyMeta($property);
                if (empty($meta["@readwrite"]) && empty($meta["@read"])){
                    throw new \Exception($normalized);
                }
                if (isset($this->$property)){
                    return $this->$property;
                }
                return null;
            }
        }
        $setMatches = StringMethods::match($name, "^set([a-zA-Z0-9]+)$");
        if ($setMatches && sizeof($setMatches) > 0){
            $normalized = lcfirst($setMatches[0]);
            $property = "_{$normalized}";
            if (property_exists($this, $property)){
                $meta = $this->_inspector->getPropertyMeta($property);
                if (empty($meta["@readwrite"]) && empty($meta["@write"])){
                    throw new \Exception($normalized);
                }
                $this->$property = $arguments[0];
                return $this;
            }
        }
    }

    public function __get($name)
    {
        $function = "get".ucfirst($name);
        return $this->$function();
    }
    
    public function __set($name, $value)
    {
        $function = "set".ucfirst($name);
        return $this->$function($value);
    }
}