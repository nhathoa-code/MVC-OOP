<?php

namespace NhatHoa\Framework\File\Storages;
use NhatHoa\Framework\File\Driver;

class FileSystem extends Driver
{
    protected $_dir;
    
    public function delete($file_path) 
    {
        if (file_exists($file_path)) {
            if (unlink($file_path)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function name()
    {
        return $this->_name;
    }

    public function originalName()
    {
        return pathinfo($this->_name, PATHINFO_FILENAME);
    }

    public function Extension()
    {
        return pathinfo($this->_name, PATHINFO_EXTENSION);
    }

    public function save($path = null,$name = null)
    {
        $fileName = $this->_name; 
        $fileTempName = $this->_tmp_name; 
        $uploadDirectory = PUBLIC_PATH . ($path === null ? "" : "/{$path}"); 
        if (!file_exists($uploadDirectory) && !is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true); 
        }
        $fileDestination = $uploadDirectory . ($name === null ? "/{$fileName}" : "/{$name}");
        if(move_uploaded_file($fileTempName, $fileDestination)) {
            return str_replace(PUBLIC_PATH . "/","",$fileDestination);
        } else {
            return null;
        }
    }

    public function dir($path)
    {   
        $this->_dir = $path;
        return $this;
    }

    public function files()
    {   
        $files = scandir(PUBLIC_PATH . "/" . $this->dir);
        natsort($files);
        return array_map(function($item){
            return url($this->_dir . "/" . $item);
        },array_values(array_filter($files,function($item){
            return !in_array($item, array('.', '..'));
        })));
    }
}