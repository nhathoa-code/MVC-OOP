<?php

namespace NhatHoa\Framework\Factories;

use NhatHoa\Framework\Base;
use NhatHoa\Framework\Registry;
use NhatHoa\Framework\File\Storages;

class FileFactory extends Base
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
    public function initialize($name, $tmp_name, $size, $error, $file_type)
    {
        $config = Registry::get("config");
        if($config){
            if(!$this->_type){
                $parsed = $config->parse(APP_PATH . "/config/file");
                if (!empty($parsed->file->default) && !empty($parsed->file->default->type)){
                    $type = $parsed->file->default->type;
                    unset($parsed->file->default->type);
                    $this->__construct(array(
                        "type" => $type,
                        "options" => (array) $parsed->file->default
                    ));
                }   
            }
        }
        
        if (!$this->_type){
            $this->_type = "file_system";
            //throw new \Exception("Invalid type");
        }
       
        switch ($this->_type)
        {
            case "file_system":
                return new Storages\FileSystem($name, $tmp_name, $size, $error, $file_type);
                break;
            default:
                throw new \Exception("Invalid type");
        }
    }
}
