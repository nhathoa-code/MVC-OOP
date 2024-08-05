<?php

namespace NhatHoa\Framework\Abstract;

abstract class Controller
{
    protected $_middleware = array();
    protected $_index = 0;

    public function middleware(array|string $middleware)
    {
        $this->_middleware[$this->_index] = array("middleware" => $middleware);
        $this->_index++;
        return $this;
    }

    public function only(array $methods)
    {
        $this->_middleware[$this->_index - 1]["only"] = $methods;
    }

    public function except(array $methods)
    {
        $this->_middleware[$this->_index - 1]["except"] = $methods;
    }

    public function before($method,$request)
    {
        if(!empty($this->_middleware)){
            foreach($this->_middleware as $item){
                if(isset($item["middleware"])){
                    if(isset($item["only"])){
                        if(in_array($method,$item["only"])){
                            if(is_string($item["middleware"])){
                                (new $item["middleware"])->handle($request);
                            }else if(is_array($item["middleware"])){
                                foreach($item["middleware"] as $mid){
                                    (new $mid)->handle($request);
                                }
                            }
                        }
                    }else if(isset($item["except"])){
                        if(!in_array($method,$item["except"])){
                            if(is_string($item["middleware"])){
                                (new $item["middleware"])->handle($request);
                            }else if(is_array($item["middleware"])){
                                foreach($item["middleware"] as $mid){
                                    (new $mid)->handle($request);
                                }
                            }
                        }
                    }else{
                        if(is_string($item["middleware"])){
                            (new $item["middleware"])->handle($request);
                        }else if(is_array($item["middleware"])){
                            foreach($item["middleware"] as $mid){
                                (new $mid)->handle($request);
                            }
                        }
                    }
                }
            }
        }
      
    }
}