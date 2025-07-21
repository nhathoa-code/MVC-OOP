<?php

namespace NhatHoa\Framework\Database\Queries;

use NhatHoa\Framework\Base;

class Mysql extends Base
{
    /**
    * @readwrite
    */
    protected $_connector;
    /**
    * @read
    */
    protected $_from;
    /**
    * @read
    */
    protected $_fields = array("*");
    /**
    * @read
    */
    protected $_limit;
    /**
    * @read
    */
    protected $_offset;
    /**
    * @read
    */
    protected $_order = array();
    /**
    * @read
    */
    protected $_groupBy = null;
    /**
    * @read
    */
    protected $_params = array();
    /**
    * @read
    */
    protected $_join = array();
    /**
    * @read
    */
    protected $_where = array();
    /**
    * @read
    */
    protected $_where_index = 1;
    /**
    * @read
    */
    protected $_distinct;
    /**
    * @read
    */
    protected $_call_when = false;
    /**
    * @readwrite
    */
    protected $_class = null;

    public function setClass($class)
    {
        $this->_class = $class;
        return $this;
    }

    public function from($from)
    {
        if (empty($from)){
            throw new \Exception("Invalid argument");
        }
        $this->_from = $from;
        return $this;
    }

    public function select(array $fields = ["*"])
    {
        $this->_fields = $fields;
        return $this;
    }

    public function join($join, $on1, $operator, $on2)
    {
        if (empty($join) || empty($on1) || empty($operator) || empty($on2)){
          throw new \Exception("Invalid argument");
        }
        $this->_join[] = "JOIN {$join} ON {$on1} {$operator} {$on2}";
        return $this;
    }

    public function leftJoin($join, $on1, $operator, $on2)
    {
        if (empty($join) || empty($on1) || empty($operator) || empty($on2)){
          throw new \Exception("Invalid argument");
        }
        $this->_join[] = "LEFT JOIN {$join} ON {$on1} {$operator} {$on2}";
        return $this;
    }

    public function limit($limit)
    {
        if (!is_int($limit) || $limit < 0){
            throw new \Exception("Invalid argument");
        }
        $this->_limit = $limit;
        return $this;
    }

    public function offset($offet)
    {
        if (!is_int($offet) || $offet < 0){
            throw new \Exception("Invalid argument");
        }
        $this->_offset = $offet;
        return $this;
    }

    public function orderBy($order, $direction = "asc")
    {
        if (empty($order)){
         throw new \Exception("Invalid argument");
        }
        $this->_order[] = "{$order} $direction";
        return $this;
    }

    public function distinct()
    {
        $this->_distinct = true;
        return $this;
    }

    public function where()
    {
        $args = func_get_args();
        $number_of_args = func_num_args();
        if($number_of_args === 1 && is_callable($args[0])){
            $this->_where[] = "(";
            $args[0]($this);
            $this->_where[] = ")";
        }else{
            if(func_num_args() < 2){
                throw new \Exception("Too less arguments,expected at least 2");
            }
            if($number_of_args === 2){
                $operator = "=";
                $column = $args[0];
                $value = $args[1];
            }else if($number_of_args === 3){
                $operator = $args[1];
                $column = $args[0];
                $value = $args[2];
            }
            $column_key = str_replace(".","_",$column);
            $this->_where[] = "{$column} {$operator} :where_{$column_key}_{$this->_where_index}";
            $this->_params[":where_{$column_key}_{$this->_where_index}"] = $value;
            $this->_where_index++;
        }
        return $this;
    }

    public function whereJsonContains($target, $candidate)
    {
        $args = func_get_args();
        if(func_num_args() < 2){
            throw new \Exception("Too less arguments,expected at least 2");
        }
        $target = $args[0];
        $candidate = $args[1];
        $this->_where[] = "JSON_CONTAINS({$target},'\"{$candidate}\"')";
        return $this;
    }

    public function orWhereJsonContains($target, $candidate)
    {
        $args = func_get_args();
        if(func_num_args() < 2){
            throw new \Exception("Too less arguments,expected at least 2");
        }
        $target = $args[0];
        $candidate = $args[1];
        $this->_where[] = "OR JSON_CONTAINS({$target},'\"{$candidate}\"')";
        return $this;
    }

    public function whereIn()
    {
        $args = func_get_args();
        $number_of_args = func_num_args();
        if($number_of_args === 1 && is_callable($args[0])){
            $this->_where[] = "(";
            $args[0]($this);
            $this->_where[] = ")";
        }else{
            if(func_num_args() < 2){
                throw new \Exception("Too less arguments,expected at least 2");
            }
            if($number_of_args === 2){
                $column = $args[0];
                $value = $args[1];
            }else if($number_of_args === 3){
                $column = $args[0];
                $value = $args[2];
            }
            $this->_where[] = "{$column} IN (" . implode(",",array_map(fn($item) => "'{$item}'",$value)) . ")";
        }
        return $this;
    }

    public function whereNotIn()
    {
        $args = func_get_args();
        $number_of_args = func_num_args();
        if($number_of_args === 1 && is_callable($args[0])){
            $this->_where[] = "(";
            $args[0]($this);
            $this->_where[] = ")";
        }else{
            if(func_num_args() < 2){
                throw new \Exception("Too less arguments,expected at least 2");
            }
            if($number_of_args === 2){
                $column = $args[0];
                $value = $args[1];
            }else if($number_of_args === 3){
                $column = $args[0];
                $value = $args[2];
            }
            $this->_where[] = "{$column} NOT IN (" . implode(",",array_map(fn($item) => "'{$item}'",$value)) . ")";
        }
        return $this;
    }

    public function whereCase(callable $callback)
    {
        $this->_where[] = "(CASE";
        $callback($this);
        $this->_where[] = "END)";
        return $this;
    }

    public function whereDate()
    {
        $args = func_get_args();
        $number_of_args = func_num_args();
        if(func_num_args() < 2){
            throw new \Exception("Too less arguments,expected at least 2");
        }
        if($number_of_args === 2){
            $operator = "=";
            $column = $args[0];
            $value = $args[1];
        }else if($number_of_args === 3){
            $operator = $args[1];
            $column = $args[0];
            $value = $args[2];
        }
        $column_key = str_replace(".","_",$column);
        $this->_where[] = "DATE({$column}) {$operator} :where_{$column_key}_{$this->_where_index}";
        $this->_params[":where_{$column_key}_{$this->_where_index}"] = $value;
        $this->_where_index++;
        return $this;
    }

    public function whereMonth()
    {
        $args = func_get_args();
        $number_of_args = func_num_args();
        if(func_num_args() < 2){
            throw new \Exception("Too less arguments,expected at least 2");
        }
        if($number_of_args === 2){
            $operator = "=";
            $column = $args[0];
            $value = $args[1];
        }else if($number_of_args === 3){
            $operator = $args[1];
            $column = $args[0];
            $value = $args[2];
        }
        $column_key = str_replace(".","_",$column);
        $this->_where[] = "MONTH({$column}) {$operator} :where_{$column_key}_{$this->_where_index}";
        $this->_params[":where_{$column_key}_{$this->_where_index}"] = $value;
        $this->_where_index++;
        return $this;
    }

    public function whereYear()
    {
        $args = func_get_args();
        $number_of_args = func_num_args();
        if(func_num_args() < 2){
            throw new \Exception("Too less arguments,expected at least 2");
        }
        if($number_of_args === 2){
            $operator = "=";
            $column = $args[0];
            $value = $args[1];
        }else if($number_of_args === 3){
            $operator = $args[1];
            $column = $args[0];
            $value = $args[2];
        }
        $column_key = str_replace(".","_",$column);
        $this->_where[] = "YEAR({$column}) {$operator} :where_{$column_key}_{$this->_where_index}";
        $this->_params[":where_{$column_key}_{$this->_where_index}"] = $value;
        $this->_where_index++;
        return $this;
    }

    public function when()
    {
        $args = func_get_args();
        $number_of_args = func_num_args();
        if(func_num_args() < 2){
            throw new \Exception("Too less arguments,expected at least 2");
        }
        if($number_of_args === 2){
            $operator = "=";
            $column = $args[0];
            $value = $args[1];
        }else if($number_of_args === 3){
            $operator = $args[1];
            $column = $args[0];
            $value = $args[2];
        }
        $column_key = str_replace(".","_",$column);
        if($this->_call_when){
            $this->_where[] = "{$column} {$operator} :where_{$column_key}_{$this->_where_index}";
        }else{
            $this->_where[] = "WHEN {$column} {$operator} :where_{$column_key}_{$this->_where_index}";
        }
        $this->_params[":where_{$column_key}_{$this->_where_index}"] = $value;
        $this->_where_index++;
        $this->_call_when = true;
        return $this;
    }

    public function whenNull($column)
    {
        if(empty($column)){
            throw new \Exception("expected one argument");
        }
        if($this->_call_when){
            $this->_where[] = "{$column} IS NULL";
        }else{
            $this->_where[] = "WHEN {$column} IS NULL";
        }
        $this->_call_when = true;
        return $this;
    }

    public function whenNotNull($column)
    {
        if(empty($column)){
            throw new \Exception("expected one argument");
        }
        if($this->_call_when){
            $this->_where[] = "{$column} IS NOT NULL";
        }else{
            $this->_where[] = "WHEN {$column} IS NOT NULL";
        }
        $this->_call_when = true;
        return $this;
    }

    public function whenElse()
    {
        $args = func_get_args();
        $number_of_args = func_num_args();
        if(func_num_args() < 2){
            throw new \Exception("Too less arguments,expected at least 2");
        }
        if($number_of_args === 2){
            $operator = "=";
            $column = $args[0];
            $value = $args[1];
        }else if($number_of_args === 3){
            $operator = $args[1];
            $column = $args[0];
            $value = $args[2];
        }
        $column_key = str_replace(".","_",$column);
        $this->_where[] = "ELSE {$column} {$operator} :where_{$column_key}_{$this->_where_index}";
        $this->_params[":where_{$column_key}_{$this->_where_index}"] = $value;
        $this->_where_index++;
        $this->_call_when = false;
    }

    public function then()
    {
        $args = func_get_args();
        $number_of_args = func_num_args();
        if(func_num_args() < 2){
            throw new \Exception("Too less arguments,expected at least 2");
        }
        if($number_of_args === 2){
            $operator = "=";
            $column = $args[0];
            $value = $args[1];
        }else if($number_of_args === 3){
            $operator = $args[1];
            $column = $args[0];
            $value = $args[2];
        }
        $column_key = str_replace(".","_",$column);
        $this->_where[] = "THEN {$column} {$operator} :where_{$column_key}_{$this->_where_index}";
        $this->_params[":where_{$column_key}_{$this->_where_index}"] = $value;
        $this->_where_index++;
        $this->_call_when = false;
    }
    

    public function orWhere()
    {
        $args = func_get_args();
        $number_of_args = func_num_args();
        if($number_of_args === 1 && is_callable($args[0])){
            $this->_where[] = "OR (";
            $args[0]($this);
            $this->_where[] = ")";
        }else{
            if(func_num_args() < 2){
                throw new \Exception("Too less arguments,expected at least 2");
            }
            $args = func_get_args();
            $number_of_args = func_num_args();
            if($number_of_args === 2){
                $operator = "=";
                $column = $args[0];
                $value = $args[1];
            }else if($number_of_args === 3){
                $operator = $args[1];
                $column = $args[0];
                $value = $args[2];
            }
            $column_key = str_replace(".","_",$column);
            $this->_where[] = "OR {$column} {$operator} :where_{$column_key}_{$this->_where_index}";
            $this->_params[":where_{$column_key}_{$this->_where_index}"] = $value;
            $this->_where_index++;
        }
        return $this;
    }

    public function whereNull($column)
    {
        if(empty($column)){
            throw new \Exception("expected one argument");
        }
        $this->_where[] = "{$column} IS NULL";
        return $this;
    }

    public function whereNotNull($column)
    {
        if(empty($column)){
            throw new \Exception("expected one argument");
        }
        $this->_where[] = "{$column} IS NOT NULL";
        return $this;
    }

    protected function _buildSelect()
    {
        $fields = array();
        $where= $order = $limit = $join ="";
        $template = "SELECT %s FROM %s %s %s %s %s %s";
        $fields = join(", ", $this->_fields);
        if($this->_distinct){
            $fields = "DISTINCT {$fields}"; 
        }
        $join = $this->_join;
        if (!empty($join)){
            $join = join(" ", $join);
        }else{
            $join = "";
        }
        $where = $this->_where;
        if (!empty($where)){
            $whereJoined = "";
            foreach($where as $index => $item){
                if($index === 0){
                    $whereJoined .= "{$item}";
                }else{
                    $whereJoined .= " AND {$item}";
                }
            }
            $where = "WHERE {$whereJoined}";
            $where = str_replace(["( AND "," AND )","AND OR","WHERE OR","(OR ","CASE AND WHEN","CASE AND","AND THEN","AND WHEN","AND END","AND ELSE"],["(",")","OR","WHERE","(","CASE WHEN","CASE","THEN","WHEN","END","ELSE"],$where);
        }else{
            $where = "";
        }
        if (!empty($this->_order)){
            $orders = join(", ",$this->_order);
            $order = "ORDER BY {$orders}";
        }
        $limit = $this->_limit;
        if (!empty($limit)){
            $offset=$this->_offset;
            if ($offset){
                $limit = "LIMIT {$offset}, {$limit}";
            }
            else{
                $limit = "LIMIT {$limit}";
            }
        }
        $groupBy = $this->_groupBy;
        if($groupBy){
            if(is_array($groupBy)){
                $groupByColumns = join(", ",$groupBy);
                $groupBy = "GROUP BY {$groupByColumns}";
            }else{
                $groupBy = "GROUP BY {$groupBy}";
            }
        }
        $sql = sprintf($template, $fields, $this->_from, $join, $where, $groupBy , $order, $limit);
        return $sql;
    }

    public function build()
    {
        return $this->_buildSelect();
    }

    protected function _buildInsert($data)
    {
        $this->_params = array();
        $fields=array();
        $values =array();
        $template ="INSERT INTO %s (%s) VALUES (%s)";
        foreach ($data as $field => $value){
            $fields[] = $field;
            $values[] = ":{$field}";
            $this->_params[":{$field}"] = $value;
        }
        $fields = join(", ", $fields);
        $values =join(", ", $values);
        return sprintf($template, $this->_from, $fields, $values);
    }

    protected function _buildUpdate($data)
    {
        $parts =array();
        $where = $limit = "";
        $template ="UPDATE %s SET %s %s %s";
        foreach ($data as $field => $value){
            $parts[] = "{$field} = :{$field}";
            $this->_params[":{$field}"] = $value;
        }
        $parts =join(", ", $parts);
        $where =$this->_where;
        if (!empty($where)){
            $joined=join(" AND ", $where);
            $where ="WHERE {$joined}";
        }
        $limit =$this->_limit;
        if (!empty($limit)){
            $offset =$this->_offset;
            $limit="LIMIT {$limit} {$offset}";
        }
        return sprintf($template, $this->_from, $parts, $where, $limit);
    }

    protected function _buildDelete()
    {
        $where = $limit= "";
        $template = "DELETE FROM %s %s %s";
        $where = $this->_where;
        if (!empty($where)){
            $joined = join(" AND ", $where);
            $where = "WHERE {$joined}";
        }
        $limit = $this->_limit;
        if (!empty($limit)){
            $limit = "LIMIT {$limit}";
        }
        return sprintf($template, $this->_from, $where, $limit);
    }

    public function get($reset = true)
    {
        $sql = $this->_buildSelect();
        $records = $this->_connector->selectAll($sql,$this->_params);
        if(!empty($records)){
            if($reset){
                $this->resetQuery();
            }
            if($this->class){
                $rows = array();
                foreach($records as $item){
                    $rows[] = new ($this->class)($item);
                }
                return $rows;
            }
            return $records;
        }
        if($reset){
            $this->resetQuery();
        }
        return [];
    }

    public function getArray($column)
    {
       return array_map(function($item) use($column){
            return $item->$column;
       },$this->get());
    }

    public function save($data)
    {
        $isInsert = sizeof($this->_where) === 0;
        if ($isInsert){
            $sql = $this->_buildInsert($data);
            $result = $this->_connector->execute($sql,$data);
        }
        else{
            $sql = $this->_buildUpdate($data);
            $result = $this->_connector->execute($sql,$this->_params);
        }
        if ($result === false){
            throw new \Exception();
        }
        if ($isInsert){
            return $this->_connector->lastInsertId;
        }
        return 0;
    }

    public function insert($data)
    {
        $sql = $this->_buildInsert($data);
        $records = $this->_connector->insert($sql,$data);
        $this->resetQuery();
        if($records){
            return $this->_connector->getLastInsertId();
        }
        return 0;
    }

    public function delete()
    {
        $sql = $this->_buildDelete();
        $result = $this->_connector->execute($sql,$this->_params);
        $this->resetQuery();
        return $result;
    }

    public function update($data)
    {
        $sql = $this->_buildUpdate($data);
        $result = $this->_connector->execute($sql,$this->_params);
        $this->resetQuery();
        return $result;
    }

    public function updateOrCreate(array $attributes,array $values)
    {
        foreach($attributes as $column => $value){
            $this->where($column,$value);
        }
        $record = $this->first(reset:false);
        if($record){
            $this->limit(1)->update($values);
        }else{
            $this->insert([...$attributes,...$values]);
        }
    }

    public function increment($column,$value)
    {
        $value = intval($value);
        $where = $limit="";
        $template ="UPDATE %s SET $column = $column + $value %s %s";
        $where =$this->_where;
        if (!empty($where)){
            $joined=join(" AND ", $where);
            $where ="WHERE {$joined}";
        }
        $limit =$this->_limit;
        if (!empty($limit)){
            $offset =$this->_offset;
            $limit="LIMIT {$limit} {$offset}";
        }
        $sql = sprintf($template, $this->_from, $where, $limit);
        $result = $this->_connector->execute($sql,$this->_params);
        $this->resetQuery();
        return $result;
    }

    public function decrement($column,$value)
    {
        $value = intval($value);
        $where =$limit="";
        $template ="UPDATE %s SET $column = $column - $value %s %s";
        $where =$this->_where;
        if (!empty($where)){
            $joined=join(" AND ", $where);
            $where ="WHERE {$joined}";
        }
        $limit =$this->_limit;
        if (!empty($limit)){
            $offset =$this->_offset;
            $limit="LIMIT {$limit} {$offset}";
        }
        $sql = sprintf($template, $this->_from, $where, $limit);
        $result = $this->_connector->execute($sql,$this->_params);
        $this->resetQuery();
        return $result;
    }

    public function first($column = null,$reset = true)
    {
        $sql = $this->_buildSelect();
        $result = $this->_connector->selectOne($sql,$this->_params);
        if($reset){
            $this->resetQuery();
        }
        if($column){
            return $result ? $result->$column : null;
        }
        if($this->_class && $result){
            return new ($this->_class)($result);
        }
        return $result;
    }

    public function count($reset = true,$context = null)
    {
        $temp_fields = $this->_fields;
        if($context){
            $this->_fields = array("COUNT({$context}) AS records");
        }else{
            $this->_fields = array("COUNT(*) AS records");
        }
        $sql = $this->_buildSelect();
        $result = $this->_connector->selectOne($sql,$this->_params);
        $this->_fields = $temp_fields;
        if($reset){
            $this->resetQuery();
        }
        return $result->records ?? 0;
    }

    public function exists()
    {
        $sql = $this->_buildSelect();
        $sql = "SELECT EXISTS({$sql}) as 'exists'";
        $result = $this->_connector->selectOne($sql,$this->_params);
        return $result->exists;
    }

    public function groupBy($columns)
    {
        $this->_groupBy = $columns;
        return $this;
    }

    public function getValue($column)
    {
       return $this->first()?->$column ?? null;
    }

    protected function resetQuery()
    {
        $this->_order = array();

        $this->_params = array();

        $this->_join = array();
 
        $this->_where = array();

        $this->_from = "";

        $this->_fields = array("*");

        $this->_limit = null;

        $this->_offset = null;

        $this->_groupBy = null;

        $this->_call_when = false;

        $this->_where_index = 1;

        $this->_model = null;
    }
}