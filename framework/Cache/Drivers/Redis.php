<?php

namespace NhatHoa\Framework\Cache\Drivers;
use NhatHoa\Framework\Cache\Driver;

class Redis extends Driver
{
    protected $_service;
    /**
    * @readwrite
    */
    protected $_host= "10.0.0.1";
    /**
    * @readwrite
    */
    protected $_port = 6379;
    /**
    * @readwrite
    */
    protected $_isConnected =false;
    protected function _isValidService()
    {
        $isEmpty = empty($this->_service);
        $isInstance =$this->_service instanceof \Predis\Client;
        if ($this->_isConnected && $isInstance && !$isEmpty){
            return true;
        }
        return false;
    }

    public function connect()
    {
        try{
            $this->_service = new \Predis\Client([
                'host' => $this->_host,
                'port' => $this->_port
            ]);
            $this->_isConnected = true;
        }catch (\Exception $e){
            throw new \Exception("Unable to connect to service");
        }
        return $this;
    }

    public function disconnect()
    {
        if ($this->_isValidService()){
            $this->_service->disconnect();
            $this->_isConnected = false;
        }
        return $this;
    }

    public function get($key, $default=null)
    {
        if (!$this->_isValidService()){
           throw new \Exception("Not connected to a valid service");
        }
        $value =$this->_service->get($key);
        if ($value){
            return $value;
        }
        return $default;
    }

    public function set($key, $value, $duration=120)
    {
        if (!$this->_isValidService()){
            throw new \Exception("Not connected to a valid service");
        }
        $this->_service->set($key, $value, "EX", $duration);
        return $this;
    }

    public function erase($key)
    {
        if (!$this->_isValidService()){
            throw new \Exception("Not connected to a valid service");
        }
        $this->_service->del($key);
        return $this;
    }
}