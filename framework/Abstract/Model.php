<?php

namespace NhatHoa\Framework\Abstract;
use NhatHoa\Framework\Registry;
use NhatHoa\Framework\Base;

class Model extends Base implements \JsonSerializable
{
    /**
    * @readwrite
    */
    protected $_connector;
    /**
    * @readwrite
    */
    protected $_table;
    /**
    * @readwrite
    */
    protected $_primary;
    /**
    * @readwrite 
    */
    protected $_columns = array();
    /**
    * @readwrite 
    */
    protected $_custom_properties = array();
    protected $_hidden = array();

    public function __debugInfo() {
        return [
            'table' => $this->_table,
            'columns' => $this->_columns,
            'primary' => $this->_primary,
            "custom_properties" => $this->_custom_properties
        ];
    }

    public function __construct($data = null)
    {
        if($data){
            $this->load($data);
        }
    }

    public function load($data)
    {
        $this->_primary = $this->getPrimary();
        $this->_table = $this->getTable();
        foreach ($data as $key => $value){
            $prop = "{$key}";
            if(!in_array($prop,$this->_hidden)){
                $this->_columns[$prop] = $value;
            }
        }
    }

    public static function getTable()
    {
        $model = new static();
        if (empty($model->_table)){
            $model->_table = explode("\\",strtolower(get_class($model)));
            $model->_table = end($model->_table) . "s";
        }
        return $model->_table;
    }

    public function getPrimary()
    {
        if(empty($this->_primary)){
            $this->_primary = "id";
        }
        return $this->_primary;
    }

    public function getColumns()
    {
        return $this->_columns;
    }

    public function getConnector()
    {
        if (empty($this->_connector)){
            $database = Registry::get("database");
            if (!$database){
                throw new \Exception("No connector availible");
            }
            $this->_connector = $database->initialize();
        }
        return $this->_connector;
    }

    public function __set($name, $value)
    {
        if($this->_primary){
            if(array_key_exists($name,$this->_columns)){
                $this->_columns[$name] = $value;
            }else{
                $this->_custom_properties[$name] = $value;
            }
        }else{
            $this->_columns[$name] = $value;
        }
    }

    public function __get($name)
    {
        if(array_key_exists($name,$this->_columns)){
            return $this->_columns[$name];
        }
        if(array_key_exists($name,$this->_custom_properties)){
            return $this->_custom_properties[$name];
        }
        return null; 
    }

    public function __isset($name)
    {
        if(isset($this->_columns[$name])){
            return true;
        }elseif(isset($this->_custom_properties[$name])){
            return true;
        }
        return false;
    }

    public function save()
    {
        $query = $this->getConnector()
        ->query()
        ->from($this->getTable());
        if (!empty($this->_primary)){
            $query->where($this->_primary, $this->_columns[$this->_primary]);
            $query->limit(1);
            $data = array_filter($this->_columns,function($item,$key){
                if($key === $this->_primary){
                    return false;
                }
                return true;
            },ARRAY_FILTER_USE_BOTH);
            $inserted_id = $query->save($data);
        }
        $inserted_id = $query->save($this->_columns);
        return $inserted_id;
    }

    public function updateOrCreate(array $attributes)
    {
        $query = $this->getConnector()->query();
        $query->from($this->getTable());
        foreach($attributes as $column => $value){
            $query->where($column,$value);
        }
        $record = $query->limit(1)->first();
        if($record){
            return true;
        }else{
            return false;
        }
    }

    public function delete()
    {
        if (!empty($this->_primary)){
            return $this->getConnector()
                ->query()
                ->from($this->getTable())
                ->where($this->_primary, $this->_columns[$this->_primary])
                ->limit(1)
                ->delete();
        }
    }

    public static function deleteAll($where = array())
    {
        $instance = new static();
        $query = $instance->getConnector()
            ->query()
            ->from($instance->getTable());
        foreach ($where as $clause => $value){
            $query->where($clause, $value);
        }
        return $query->delete();
    }

    public static function all($where = array(), $whereIn = array(), $whereNotIn = array(), $whereNull = array(), $whereNotNull = array(), $select = array("*"),$orderBy = array(), $offset = null, $limit = null, $page = null)
    {
        $model = new static();
        return $model->_all($where, $whereIn, $whereNotIn, $whereNull, $whereNotNull, $select, $orderBy, $offset, $limit, $page);
    }

    protected function _all($where = array(), $whereIn = array(), $whereNotIn = array(), $whereNull = array(), $whereNotNull = array(), $select = array("*"),$orderBy = array(), $offset = null, $limit = null, $page = null)
    {
        $query = $this
            ->getConnector()
            ->query()
            ->from($this->getTable())
            ->select($select);
        foreach ($where as $column => $value){
            if(is_array($value)){
                $operator = $value["operator"];
                unset($value["operator"]);
                foreach($value as $column => $val){
                    $query->where($column,$operator,$val);
                }
            }else{
                $query->where($column, $value);
            }
        }
        foreach ($whereIn as $column => $in){
            $query->whereIn($column, $in);
        }
        foreach ($whereNotIn as $column => $in){
            $query->whereNotIn($column, $in);
        }
        foreach ($whereNull as $value){
            $query->whereNull($value);
        }
        foreach ($whereNotNull as $value){
            $query->whereNotNull($value);
        }
        if ($offset != null){
            $query->offset($offset);
        }
        foreach ($orderBy as $column => $direction){
            $query->orderBy($column, $direction);
        }
        if ($limit != null){
            $query->limit($limit, $page);
        }
        $rows = array();
        $class = get_class($this);
        foreach ($query->get() as $row){  
            $rows[] = new $class($row);
        }
        return $rows;
    }

    public static function first($where = array(), $whereNull = array(),$whereNotNull = array() ,$order = null, $direction = null)
    {
        $model = new static();
        return $model->_first($where, $whereNull, $whereNotNull, $order, $direction);
    }

    protected function _first($where = array(), $whereNull = array(),$whereNotNull = array(), $order = null, $direction = null)
    {
        $query = $this
            ->getConnector()
            ->query()
            ->from($this->getTable());
        foreach ($where as $clause => $value){
            $query->where($clause, $value);
        }
        foreach ($whereNull as $column){
            $query->whereNull($column);
        }
        foreach ($whereNotNull as $column){
            $query->whereNotNull($column);
        }
        if ($order != null){
            $query->order($order, $direction);
        }
        $first = $query->first();
        $class = get_class($this);
        if ($first){
            return new $class($first);
        }
        return null;
    }

    public static function table($table)
    {
        $model = new static();
        return $model->_table($table);
    }

    protected function _table($table)
    {
        $query = $this
            ->getConnector()
            ->query()
            ->from($table);
        return $query;
    }

    public static function where($column,$operator,$value)
    {
        $model = new static();
        return $model->_where($column,$operator,$value);
    }

    protected function _where($column,$operator,$value)
    {
        $query = $this
            ->getConnector()
            ->query()
            ->from($this->getTable())
            ->setClass(get_class($this))
            ->where($column,$operator,$value);
        return $query;
    }

    public static function query()
    {
        $model = new static();
        return $model->_query();
    }

    protected function _query()
    {
        $query = $this
            ->getConnector()
            ->query()
            ->from($this->getTable())
            ->setClass(get_class($this));
        return $query;
    }

    public static function count($table = null, $where = array(), $whereNull = array(), $limit = null)
    {
        $model = new static();
        return $model->_count($table, $where, $whereNull, $limit);
    }

    protected function _count($table = null, $where = array(), $whereNull = array(), $limit = null)
    {
        $query = $this
            ->getConnector()
            ->query()
            ->from($table ? $table : $this->getTable());
        foreach ($where as $column => $value){
            if(is_array($value)){
                $operator = $value["operator"];
                unset($value["operator"]);
                foreach($value as $column => $val){
                    $query->where($column,$operator,$val);
                }
            }else{
                $query->where($column, $value);
            }
        }
        foreach ($whereNull as $column){
            $query->whereNull($column);
        }
        if($limit){
            $query->limit($limit);
        }
        return $query->count();
    }

    public static function exists($table = null, $where = array(), $whereNull = array())
    {
        $model = new static();
        return $model->_exists($table, $where, $whereNull);
    }

    protected function _exists($table = null, $where = array(), $whereNull = array())
    {
        $query = $this
            ->getConnector()
            ->query()
            ->from($table ? $table : $this->getTable());
        foreach ($where as $column => $value){
            if(is_array($value)){
                $operator = $value["operator"];
                unset($value["operator"]);
                foreach($value as $column => $val){
                    $query->where($column,$operator,$val);
                }
            }else{
                $query->where($column, $value);
            }
        }
        foreach ($whereNull as $column){
            $query->whereNull($column);
        }
        return $query->exists();
    }

    public static function limit($limit)
    {
        $model = new static();
        return $model->_limit($limit);
    }

    protected function _limit($limit)
    {
        $query = $this
            ->getConnector()
            ->query()
            ->from($this->getTable())
            ->setClass(get_class($this))
            ->limit($limit);
        return $query;
    }

    public static function orderBy($column,$direction)
    {
        $model = new static();
        return $model->_orderBy($column,$direction);
    }

    protected function _orderBy($column,$direction)
    {
        $query = $this
            ->getConnector()
            ->query()
            ->from($this->getTable())
            ->setClass(get_class($this))
            ->orderBy($column,$direction);
        return $query;
    }

    public function join($table,$column1,$on,$column2)
    {
        $query = $this
            ->getConnector()
            ->query()
            ->from($this->getTable())
            ->setClass(get_class($this))
            ->join($table,$column1,$on,$column2);
        return $query;
    }

    public function leftJoin($table,$column1,$on,$column2)
    {
        $query = $this
            ->getConnector()
            ->query()
            ->from($this->getTable())
            ->setClass(get_class($this))
            ->leftJoin($table,$column1,$on,$column2);
        return $query;
    }

    public function jsonSerialize() : array
    {
        return [...$this->_columns,...$this->_custom_properties];
    }
}