<?php

namespace NhatHoa\Framework\Core;
use NhatHoa\Framework\Base;
use NhatHoa\Framework\Registry;
use NhatHoa\App\Middlewares\CSRF;

class Router extends Base implements \Serializable
{
    /**
     * @none
     */
    private $routes = [
        'GET' => [],
        'POST' => []
    ];
    /**
     * @none
     */
    private $prefixes = [];
    /**
     * @none
     */
    private $groupMiddlewares = [];

    public function get($route,$action,$middleware = null)
    {
        $route = preg_replace('/\/\{([^\/]+)\}/', '/(?<$1>[0-9a-zA-Z-_/]+)', $route);
        if(count($this->prefixes) > 0){
            $route = implode('', $this->prefixes) . ($route === "/" ? "" : $route);
        }
        $actions["action"] = $action;
        $middlewares = array();
        if(!empty($this->groupMiddlewares)){
            foreach($this->groupMiddlewares as $item){
                array_push($middlewares,$item);
            }
        }
        if($middleware){
            if(is_array($middleware)){
               if(array_key_exists("without",$middleware)){
                    $without = $middleware['without'];
                    unset($middleware["without"]);
               } 
               foreach($middleware as $item){
                    array_push($middlewares,$item);
               } 
            }else{
                array_push($middlewares,$middleware);
            }
            if(isset($without)){
                if(is_array($without)){
                    $middlewares = array_diff($middlewares,$without);
                }
            }
        }
        $this->routes['GET'][$route] = [
            'action' => $action,
            'middlewares' => $middlewares,
        ];
        return $this;
    }

    public function post($route,$action,$middleware = null)
    {
        $route = preg_replace('/\/\{([^\/]+)\}/', '/(?<$1>[^\/]+)', $route);
        if(count($this->prefixes) > 0){
            $route = implode('', $this->prefixes) . $route;
        }
        $actions["action"] = $action;
        $middlewares = array();
        if(!empty($this->groupMiddlewares)){
            foreach($this->groupMiddlewares as $item){
                array_push($middlewares,$item);
            }
        }
        if($middleware){
            if(is_array($middleware)){
                if(array_key_exists("without",$middleware)){
                    $without = $middleware['without'];
                    unset($middleware["without"]);
                } 
                foreach($middleware as $item){
                    array_push($middlewares,$item);
                } 
            }else{
                array_push($middlewares,$middleware);
            }
            if(isset($without)){
                if(is_array($without)){
                    $middlewares = array_diff($middlewares,$without);
                }
            }
        }
        $this->routes['POST'][$route] = [
            'action' => $action,
            'middlewares' => $middlewares,
        ];
        return $this;
    }

    public function group($prefix,$callback,$middleware = null)
    {
        $this->prefixes[] = "/{$prefix}";
        if($middleware){
            if(is_array($middleware)){
                foreach($middleware as $item){
                   array_push($this->groupMiddlewares,$item);
                }
            }else{
                array_push($this->groupMiddlewares,$middleware);
            }
        }
        $callback();
        array_pop($this->prefixes);
        if($middleware){
            if(is_array($middleware)){
                array_splice($this->groupMiddlewares, -count($middleware));
            }else{
                array_splice($this->groupMiddlewares,-1);
            }
        }
    }

    public function dispatch()
    {
        $is_route_found = false;
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $base_path = $_GET['url'] ?? "/";
        $requestedRoute = rtrim($base_path,'/');
        foreach($this->routes[$requestMethod] as $route => $actions){
            $action = $actions['action'];
            $middlewares = $actions['middlewares'];
            $pattern = "@^" . trim($route,"/") . "$@";
            preg_match($pattern,$requestedRoute,$matches);
            if($matches){
                $request = new Request(new Validator(new Response()));
                $service_container = Registry::get("service_container");
                $service_container->set(Request::class,function() use($request){
                    return $request;
                });
                $is_route_found = true;
                $csrfInstance = new CSRF;
                $request = $csrfInstance->handle($request);
                $params = array_filter($matches,"is_string", ARRAY_FILTER_USE_KEY);
                foreach($middlewares as $item){
                    $middlewareInstance = new $item();
                    $middlewareInstance->handle($request);
                }
                if(is_callable($action)){
                    $return = call_user_func_array($action,$params);
                }elseif(is_array($action)){
                    if(count($action) < 2){
                        throw new \Exception("Invalid class's method");
                    }
                    list($controllerClass,$method) = $action;
                    if (method_exists($controllerClass, $method)) {
                        $controllerInstance = $service_container->get($controllerClass);
                        $dependencies = $service_container->resolveMethodDependencies($controllerInstance,$method,$params);
                        if(method_exists($controllerInstance,"before")){
                            $controllerInstance->before($method,$request);
                        }
                        $return = call_user_func_array([$controllerInstance, $method],$dependencies);
                        if($return){
                            if(is_array($return) || is_object($return)){
                                var_dump($return);
                            }else{
                                echo $return;
                            }
                        }
                    }else{
                        throw new \Exception("method not exists");
                    }
                }
                break;
            }
        }
        if(!$is_route_found){
            throw new \Exception("route not found");
        }
    }

    public function serialize()
    {
        return serialize($this->routes);
    }

    public function unserialize(string $data)
    {
        $this->routes = unserialize($data);
    }
}