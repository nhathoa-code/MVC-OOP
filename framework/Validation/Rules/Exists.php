<?php

namespace NhatHoa\Framework\Validation\Rules;

use NhatHoa\Framework\Abstract\Rule;
use NhatHoa\Framework\Core\Validator;
use NhatHoa\Framework\Registry;

class Exists implements Rule
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
            "rule" => "exists",
            "message" => "Giá trị không tồn tại !"
        );
        return $validator->_common($field,$rule_message,function($data) use($field){
            $table = $this->_constraints[0];
            $column = $this->_constraints[1] ?? null;
            $query = $this->_query->from($table);
            if($column){
                $query->where($column,$data);
            }
            if(!$query->first()){
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