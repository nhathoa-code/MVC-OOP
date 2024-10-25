<?php

namespace NhatHoa\Framework\Core;
use NhatHoa\Framework\Base;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Core\Response;
use NhatHoa\Framework\Abstract\Rule;
use NhatHoa\Framework\Registry;

class Validator extends Base
{
    protected $_extensions = array(
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        'image/webp' => 'webp',
    );
    /**
     * @readwrite
     */
    protected $_errors = array();
    /**
     * @readwrite
     */
    protected $_data = array();
    /**
     * @readwrite
     */
    protected Request $_request;
    /**
     * @readwrite
     */
    protected Response $_response;
    protected $_session;
    protected $_bail;
    public function __construct(Response $response)
    {
        parent::__construct(array(
            "data" => [...$_GET,...$_POST,...$_FILES],
            "response" => $response,
            "request" => new Request($this)
        ));
        $this->_session = Registry::get("session");
    }

    protected function _getData($field)
    {
        if(strpos($field,".")){
            $data = $this->_data;
            $parts = explode(".",$field);
            $last_index = count($parts) - 1;
            foreach($parts as $index => $item){
                if($index === $last_index && $item === "*"){
                    return $data;
                }
                if($index === 0){
                    if(isset($_FILES[$item])){
                        $data = $_FILES[$item]['tmp_name'];
                    }else{
                        if(isset($data[$item])){
                            $data = $data[$item];
                        }else{
                            return null;
                        }
                    }
                }else{
                    if(isset($data[$item])){
                        $data = $data[$item];
                    }else{
                        return null;
                    }  
                }
            }
            return $data;
        }else{
            if(isset($_FILES[$field])){
                return $_FILES[$field]['tmp_name'];
            }
            return $this->_data[$field] ?? null;
        }
    }

    protected function _required($field)
    {
        $rule_message = array(
            "rule" => "required",
            "message" => "Vui lòng nhập vào trường này"
        );
        return $this->_common($field,$rule_message,function($data){
            if($data === "" || $data === null){
                return true;
            }
            return false;
        });
    }

    protected function _email($field)
    {
        $rule_message = array(
            "rule" => "email",
            "message" => "Email không hợp lệ"
        );
        return $this->_common($field,$rule_message,function($data){
            return !filter_var($data, FILTER_VALIDATE_EMAIL);
        });
    }

    protected function _integer($field)
    {
        $rule_message = array(
            "rule" => "integer",
            "message" => "Giá trị phải là số nguyên"
        );
        return $this->_common($field,$rule_message,function($data){
            if(filter_var($data, FILTER_VALIDATE_INT) === false){
                return true;
            }
            return false;
        });
    }
    
    protected function _min($field,$constraints,$message,$rules)
    {
        $is_string = in_array("string",$rules);
        $is_numeric = false;
        $array_to_check = ["numeric","integer"];
        foreach($array_to_check as $item){
            if(in_array($item,$rules)){
                $is_numeric = true;
                break;
            }
        }
        $rule_message = array(
            "rule" => "min",
            "message" => $message ?? "Giá trị không được nhỏ hơn {$constraints[0]}" . ($is_string ? " ký tự" : "")
        );
        return $this->_common($field,$rule_message,function($data) use($constraints,$is_string,$is_numeric){
            if($is_numeric){
                return (float) $data < (float) $constraints[0];
            }elseif($is_string){
                return strlen($data) < (int) $constraints[0];
            }
        });
    }

    protected function _max($field,$constraints,$message,$rules)
    {
        $is_string = in_array("string",$rules);
        $is_numeric = false;
        $array_to_check = ["numeric","integer"];
        foreach($array_to_check as $item){
            if(in_array($item,$rules)){
                $is_numeric = true;
                break;
            }
        }
        $rule_message = array(
            "rule" => "max",
            "message" => $message ?? "Giá trị không được lớn hơn {$constraints[0]}" . ($is_string ? " ký tự" : "")
        );
        return $this->_common($field,$rule_message,function($data) use($constraints,$is_string,$is_numeric){
            if($is_numeric){
                return (float) $data > (float) $constraints[0];
            }elseif($is_string){
                return strlen($data) > (int) $constraints[0];
            }
           
        });
    }

    protected function _image($field)
    {
        $rule_message = array(
            "rule" => "image",
            "message" => "Giá trị phải là định dạng tập tin ảnh"
        );
        return $this->_common($field,$rule_message,function($data){
            if(!$data){
                return true;
            }
            $mime_type = mime_content_type($data);
            if (!$mime_type || strpos($mime_type, 'image/') !== 0) {
                return true;
            }
            return false;
        });
    }

    protected function _mimes($field,$constraints)
    {
        $rule_message = array(
            "rule" => "mimes",
            "message" => "Tập tin không đúng định dạng"
        );
        return $this->_common($field,$rule_message,function($data) use($constraints){
            if(!$data){
                return true;
            }
            $mime_type = mime_content_type($data);
            if (!$mime_type) {
                return true;
            }
            $extension = $this->_extensions[$mime_type];
            if(!in_array($extension,$constraints)){
                return true;
            }
            return false;
        });
    }

    protected function _numeric($field)
    {
        $rule_message = array(
            "rule" => "numeric",
            "message" => "Giá trị phải là định dạng số"
        );
        return $this->_common($field,$rule_message,function($data){
            if(!is_numeric($data)){
                return true;
            }
            return false;
        });
    }

    protected function _string($field)
    {
        $rule_message = array(
            "rule" => "string",
            "message" => "Giá trị phải là định dạng chuỗi"
        );
        return $this->_common($field,$rule_message,function($data){
            if(!is_string($data)){
                return true;
            }
            return false;
        });
    }


    protected function _array($field)
    {
        $rule_message = array(
            "rule" => "array",
            "message" => "Giá trị phải là cấu trúc mảng"
        );
        return $this->_common($field,$rule_message,function($data){
            if(!$data || !is_array($data)){
                return true;
            }
            return false;
        });
    }

    protected function _arraySameLength($field,$constraints)
    {
        $rule_message = array(
            "rule" => "arraySameLength",
            "message" => "Sô phần tử phải bằng với \"{$constraints[0]}\""
        );
        return $this->_common($field,$rule_message,function() use($field,$constraints){
            $data = $this->_getData($field);
            if(!$data || !is_array($data)){
                return true;
            }
            $another_data = $this->_getData($constraints[0]);
            if(!$another_data || !is_array($another_data)){
                return true;
            }
            if(count($data) !== count($another_data)){
                return true;
            }
            return false;
        });
    }

    protected function _date($field)
    {
        $rule_message = array(
            "rule" => "date",
            "message" => "Giá trị phải là định dạng \"ngày\""
        );
        return $this->_common($field,$rule_message,function($data){
            if(!$data || !strtotime($data)){
                return true;
            }
            return false;
        });
    }

    protected function _date_format($field,$constraints)
    {
        $rule_message = array(
            "rule" => "date_format",
            "message" => "Giá trị phải là định dạng \"{$constraints[0]}\""
        );
        return $this->_common($field,$rule_message,function($data) use($constraints){
            $format = $constraints[0];
            $dateTime = \DateTime::createFromFormat($format, $data);
            if ($dateTime !== false && $dateTime->format($format) === $data) {  
                return false;
            }
            return true;
        });
    }

    protected function _after($field,$constraints)
    {
        $rule_message = array(
            "rule" => "after",
            "message" => "Thời gian này phải lớn hơn trường \"{$constraints[0]}\""
        );
        return $this->_common($field,$rule_message,function($data) use($constraints){
            $another_date = $this->_getData($constraints[0]);
            $timestamp_of_another = strtotime($another_date);
            $timestamp = strtotime($data);
            if(!$timestamp_of_another || !$timestamp){
                return true;
            }
            if ($timestamp > $timestamp_of_another) {  
                return false;
            }
            return true;
        });
    }

    protected function _json($field)
    {
        $rule_message = array(
            "rule" => "json",
            "message" => "Giá trị phải là định dạng json"
        );
        return $this->_common($field,$rule_message,function($data){
            if(json_decode($data)){
                return false;
            }
            return true;
        });
    }

    protected function _gt($field,$constraints)
    {
        $rule_message = array(
            "rule" => "gt",
            "message" => "Giá trị của trường này phải lớn hơn trường \"{$constraints[0]}\""
        );
        return $this->_common($field,$rule_message,function($data) use($constraints){
            $value = $this->_getData($constraints[0]);
            if($data > $value){
                return false;
            }
            return true;
        });
    }

    protected function _unique($field,$constraints)
    {
        $connector = Registry::get("database");
        $rule_message = array(
            "rule" => "unique",
            "message" => "Giá trị này đã tồn tại trên hệ thống"
        );
        return $this->_common($field,$rule_message,function($data) use($constraints,$field,$connector){
            $table = $constraints[0];
            $column = $constraints[1] ?? null;
            $ignore = $constraints[2] ?? null;
            $query = $connector->query()->from($table);
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

    protected function _distinct($field)
    {
        $rule_message = array(
            "rule" => "unique",
            "message" => "Mỗi phần tử trong mãng phải có giá trị duy nhất"
        );
        return $this->_common($field,$rule_message,function($data) use($field){
            $data = $this->_getData($field);
            if(!is_array($data)){
                return true;
            }
            $uniqueArray = array_unique($data);
            if (count($data) === count($uniqueArray)) {
                return false;
            } else {
                return true;
            }
        });
    }

    protected function _in($field,$constraints)
    {
        $rule_message = array(
            "rule" => "in",
            "message" => "Giá trị không cho phép"
        );
        return $this->_common($field,$rule_message,function($data) use($constraints){
            if(!in_array($data,$constraints)){
                return true;
            }
            return false;
        });
    }

    protected function _exists($field,$constraints)
    {
        $connector = Registry::get("database");
        $rule_message = array(
            "rule" => "exists",
            "message" => "Giá trị không tồn tại trên hệ thống"
        );
        return $this->_common($field,$rule_message,function($data) use($constraints,$connector,$field){
            $table = $constraints[0];
            $column = $constraints[1] ?? $field;
            $query = $connector->query()->from($table);
            if(!$query->where($column,$data)->first()){
                return true;
            }
            return false;
        });
    }

    protected function _regex($field,$constraints,$message)
    {
        $rule_message = array(
            "rule" => "regex",
            "message" => $message ?? "Giá trị không hợp lệ"
        );
        return $this->_common($field,$rule_message,function($data) use($constraints){
            $pattern = implode(",",$constraints);
            if(!preg_match($pattern,$data)){
                return true;
            }
            return false;
        });
    }

    protected function _same($field,$constraints)
    {
        $rule_message = array(
            "rule" => "same",
            "message" => "Trường này phải trùng với {$constraints[0]}"
        );
        return $this->_common($field,$rule_message,function($data) use($constraints){
            $another_field = $constraints[0];
            $another_data = $this->_getData($another_field);
            if($data !== $another_data){
                return true;
            }
            return false;
        });
    }

    public function _common($field,$rule_message,callable $callback)
    {
        $data = $this->_getData($field);
        if(is_array($data)){
            if($rule_message["rule"] !== "array"){
                $errors = array();
                foreach($data as $index => $item){
                    if($callback($item)){
                        $errors[$index] = $rule_message['message'];
                    }
                }
            }
        }else{
            if($callback($data)){
                $errors = $rule_message['message'];
            }
        }
        if(!empty($errors)){
            if(strpos($field,".")){
                $ref = &$this->_errors;
                $parts = explode(".",$field);
                $last_index = count($parts) - 1;
                foreach($parts as $index => $item){
                    if($last_index === $index && $item === "*"){
                        $ref[$rule_message['rule']] = $errors;
                        return;
                    }else{
                        if(!isset($ref[$item])){
                            $ref[$item] = array();
                        }
                        $ref = &$ref[$item];
                    }
                }
                $ref[$rule_message['rule']] = $errors;
            }else{
                $this->_errors[$field][$rule_message['rule']] = $errors;
            }
            return false;
        }
        return true;
    }


    public function validate(array $fields_rules, array $messages = array())
    {
        foreach($fields_rules as $field => $rule_string)
        {
            $this->_bail = false;
            if(is_array($rule_string)){
                $rules = $rule_string;
                if($rules[0] === "bail"){
                    $this->_bail = true;
                    unset($rules[0]);
                }
            }else{ 
                $rule_string = trim($rule_string);
                if(strpos($rule_string,"bail") === 0){
                    $this->_bail = true;
                    $rule_string = str_replace("bail|","",$rule_string);
                }
                $rules = explode("|",$rule_string);
            }
            $required = false;
            $has_required_without = false;
            foreach ($rules as $index => $rule) {
                if(is_string($rule)){
                    if (preg_match("/^required_without:[^,]+$/", $rule)) {
                        $has_required_without = true;
                        $parts = explode(":",$rule);
                        $field_to_check = $parts[1];
                        if(!$this->_getData($field_to_check)){
                            $rules[$index] = "required";
                            $required = true;
                        }
                        break;
                    }
                }
            }
            if(!$required && $has_required_without){
                continue;
            }
            foreach($rules as $index => $rule){
                if(is_string($rule)){
                    $rule = trim($rule);
                    if($rule === "nullable" && $index === 0){
                        if(!$this->_getData($field)){
                            break;
                        }else{
                            continue;
                        }
                    }
                    if(strpos($rule,":")){
                        $parts = explode(":",$rule,2);
                        $rule = $parts[0];
                        $constraints = $parts[1];
                    }
                    $method = "_" . $rule;
                    if(method_exists($this,$method)){
                        if(isset($constraints)){
                            $constraints_arr = explode(",",$constraints);
                            $check = $this->$method($field,$constraints_arr,$messages["{$field}.{$rule}"] ?? null,$rules);
                        }else{
                            $check = $this->$method($field,$messages["{$field}.{$rule}"] ?? null,$rules);
                        }
                        if($check === false && $this->_bail === true){
                            break;
                        }
                    }else{
                        throw new \Exception("method not found");
                    }
                }elseif($rule instanceof Rule){
                    $check = $rule->validate($this,$field);
                    if($check === false && $this->_bail === true){
                        break;
                    }
                }
            }
        }
        return $this->_checkValidate();
    }

    protected function _checkValidate()
    {
        if(!empty($this->_errors)){
            if($this->_request->isAjax()){
                return $this->_response->json(["errors" => $this->errors],400);
            }else{   
                $this->_session->set("errors",$this->_errors);
                $this->_session->set("old",$this->_request->postData());
                return $this->_response->backNow();
            }
        }
        $getInputs = filter_input_array(INPUT_GET);
        $postInputs = filter_input_array(INPUT_POST);
        $validated = array_merge((array) $getInputs, (array) $postInputs);
        unset($validated['csrf_token']);
        return $validated;
    }
}
