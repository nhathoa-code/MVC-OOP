<?php

namespace NhatHoa\Framework\Validation\Rules;

use NhatHoa\Framework\Abstract\Rule;
use NhatHoa\Framework\Core\Validator;
use NhatHoa\Framework\Registry;

class Unique implements Rule
{
    protected $_query;
    protected $_constraints;

    public function __construct($constraints)
    {   
        $this->_constraints = $constraints;
    }

    public function validate(Validator $validator, $field = null)
    {
        $rule_message = array(
            "rule" => "unique",
            "message" => "Giá trị này đã tồn tại trên hệ thống"
        );
        return $validator->_common($field,$rule_message,function($data) use($field){
            $table = $this->_constraints[0];
            $column = $this->_constraints[1] ?? null;
            $ignore = $this->_constraints[2] ?? null;
            $query = $this->_query->from($table);
            if($column){
                $query->where($column,$data);
            }else{
                $query->where($field,$data);
            }
            if($ignore){
                $query->where("id","!=",$ignore);
            }
            if($query->first()){
                return true;
            }
            return false;
        });
    }

    public function where(callable $callback)
    {
        $connector = Registry::get("database");
        $this->_query = $callback($connector->query());
        return $this;
    }
}