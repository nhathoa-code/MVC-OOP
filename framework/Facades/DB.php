<?php

namespace NhatHoa\Framework\Facades;

use NhatHoa\Framework\Registry;

class DB
{
    protected $_connector;

    public function __construct()
    {
        $this->_connector = Registry::get("database");
    }

    public static function table($table)
    {
        $db = new self();
        $query = $db->_connector->query()->from($table);
        return $query;
    }

    public static function beginTransaction()
    {
        $db = new self();
        $database = $db->_connector->connect()->getPDO();
        $database->beginTransaction();
    }

    public static function commit()
    {
        $db = new self();
        $database = $db->_connector->connect()->getPDO();
        $database->commit();
    }

    public static function rollBack()
    {
        $db = new self();
        $database = $db->_connector->connect()->getPDO();
        $database->rollBack();
    }
}