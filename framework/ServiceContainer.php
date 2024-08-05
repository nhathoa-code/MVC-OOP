<?php

namespace NhatHoa\Framework;

class ServiceContainer extends Base
{
    private array $_registry = [];

    public function set(string $name, \Closure $value): void
    {
        $this->_registry[$name] = $value;
    }

    public function get(string $class_name): object
    {
        if (array_key_exists($class_name, $this->_registry)) {
            return $this->_registry[$class_name]();
        }
    
        $reflector = new \ReflectionClass($class_name);

        if(!$reflector){
            throw new \Exception("invalid class");
        }
        
        $constructor = $reflector->getConstructor();

        if($constructor){
            $declaring_class = $constructor->getDeclaringClass()->getName();
        }

        if ($constructor === null || $class_name !== $declaring_class) {
            return new $class_name;
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            $dependencies[] = $this->get((string) $type);
        }

        return new $class_name(...$dependencies);
    }

    public function resolveMethodDependencies(object $object,string $method,array $params)
    {
        $reflectionMethod = new \ReflectionMethod($object, $method);
        $dependencies = [];
   
        foreach ($reflectionMethod->getParameters() as $parameter) {
            if ((string) $parameter->getType() !== "") {
                $dependency = (string) $parameter->getType();
                $dependencies[] = $this->get($dependency);
            } elseif (array_key_exists($parameter->name, $params)) {
                $dependencies[] = $params[$parameter->name];
            } else {
                throw new \Exception("Unable to resolve dependency for parameter: {$parameter->name}");
            }
        }
        return $dependencies;
    }
}