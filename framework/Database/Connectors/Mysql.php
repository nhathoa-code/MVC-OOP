<?php

namespace NhatHoa\Framework\Database\Connectors;

use NhatHoa\Framework\Database\Connector;
use NhatHoa\Framework\Database\Queries;
use PDOException;

class Mysql extends Connector
{
    protected $_service;
    /**
    * @readwrite
    */
    protected $_dbname;
    /**
    * @readwrite
    */
    protected $_host;
    /**
    * @readwrite
    */
    protected $_username;
    /**
    * @readwrite
    */
    protected $_password;
    /**
    * @readwrite
    */
    protected $_schema;
    /**
    * @readwrite
    */
    protected $_port;
    /**
    * @readwrite
    */
    protected $_charset = "utf8";
    /**
    * @readwrite
    */
    protected $_engine = "InnoDB";
    /**
    * @readwrite
    */
    protected $_isConnected =false;
    /**
    * @read
    */
    protected $_last_statement;
    // checks if connected to the database
    protected function _isValidService()
    {
        $isEmpty = empty($this->_service);
        $isInstance =$this->_service instanceof \PDO;
        if ($this->_isConnected && $isInstance && !$isEmpty){
            return true;
        }
        return false;
    }

    // connects to the database
    public function connect()
    {
        if (!$this->_isValidService()){
            try{
                $this->_service = new \PDO(
                    "mysql:host={$this->_host};dbname={$this->_dbname};port={$this->_port};charset={$this->_charset};engine={$this->_engine}",
                    $this->_username,
                    $this->_password
                );
            }catch(PDOException $e){
                throw new \Exception("Unable to connect to service " . $e->getMessage());
            }
            $this->_isConnected = true;
        }
        return $this;
    }

    // disconnects from the database
    public function disconnect()
    {
        if ($this->_isValidService()){
            $this->_isConnected=false;
            $this->_service = null;
        }
        return $this;
    }

    // returns a corresponding query instance
    public function query()
    {
        return new Queries\Mysql(array(
            "connector" => $this->connect()
        ));
    }

    public function getPdo()
    {
        return $this->_service;
    }

    public function insert($sql,$data)
    {
        $this->_service->prepare($sql)->execute($data);
        return $this->getLastInsertId();
    }
    
    // executes the provided SQL statement
    public function execute($sql,$params = [])
    {
        if (!$this->_isValidService()){
            throw new \Exception("Not connected to a valid service");
        }
        try {
            $statement = $this->_service->prepare($sql);
            $statement->execute($params);
            return $statement->rowCount();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function exec($sql)
    {
        $this->_service->exec($sql);
    }

    // returns the ID of the last row
    // to be inserted
    public function getLastInsertId()
    {
        if (!$this->_isValidService()){
            throw new \Exception("Not connected to a valid service");
        }
        return $this->_service->lastInsertId();
    }
    
    // returns the number of rows affected
    // by the last SQL query executed
    public function getAffectedRows()
    {
        if (!$this->_isValidService()){
            throw new \Exception("Not connected to a valid service");
        }
        return $this->_last_statement->rowCount();
    }

    public function selectOne($sql,$params = [])
    {
        try {
            $statement = $this->_service->prepare($sql);
            $statement->execute($params);
            return $statement->fetch(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function selectAll($sql,$params = [])
    {
        try {
            $statement = $this->_service->prepare($sql);
            $statement->execute($params);
            $this->_last_statement = $statement;
            return $statement->fetchAll(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            return null;
        }
    }

    // returns the last error of occur
    public function getLastError()
    {
        if (!$this->_isValidService()){
            throw new \Exception("Not connected to a valid service");
        }
        return $this->_service->errorInfo();
    }

}