<?php

namespace NhatHoa\Framework\Core;
use NhatHoa\Framework\Base;
use NhatHoa\Framework\Factories\FileFactory;
use NhatHoa\Framework\Registry;

class Request extends Base
{
    protected Validator|null $_validator; 

    public function __construct(Validator|null $validator = null)
    {
        parent::__construct();
        $this->_validator = $validator;
    }

    public function method() 
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function uri() 
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function input($name)
    {
        if(isset($_REQUEST[$name])){
            return $_REQUEST[$name];
        }
        return null;
    }

    public function has($name)
    {
        return isset($_REQUEST[$name]);
    }

    public function file($name)
    {
        if(isset($_FILES[$name])){
            $file_factory = new FileFactory();
            if(is_array($_FILES[$name]['name'])){
                $files = array();
                $fileCount = count($_FILES[$name]['name']);
                for ($i = 0; $i < $fileCount; $i++) {
                    $file_name = $_FILES[$name]['name'][$i];
                    $tmp_name = $_FILES[$name]['tmp_name'][$i];
                    $size = $_FILES[$name]['size'][$i];
                    $error = $_FILES[$name]['error'][$i];
                    $file_type = $_FILES[$name]['type'][$i];
                    $files[] = $file_factory->initialize($file_name, $tmp_name, $size, $error, $file_type);
                }
                return $files;
            }else{
                return $file_factory->initialize($_FILES[$name]['name'],$_FILES[$name]['tmp_name'],$_FILES[$name]['size'],$_FILES[$name]['error'],$_FILES[$name]['type']);
            }
        }
        return null;
    }

    public function session()
    {
        return Registry::get("session");
    }

    public function hasFile($name)
    {
        return isset($_FILES[$name]) && !empty($_FILES[$name]['name'][0]);
    }

    public function all()
    {
        return [...$_REQUEST,...$_FILES];
    }

    public function postData()
    {
        return $_POST;
    }

    public function post($name)
    {
        return $_POST[$name] ?? null;
    }

    public function query($name)
    {
        return $_GET[$name] ?? null;
    }

    public function get($name)
    {
        return $this->query($name);
    }

    public function hasQuery($name)
    {
        return isset($_GET[$name]);
    }

    public function isAjax()
    {
       return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function validate(array $fields_rules, array $message = array())
    {
        return $this->_validator->validate($fields_rules, $message);
    }
}