<?php

namespace NhatHoa\App\Validations;
use NhatHoa\Framework\core\Request;

class CouponValidation
{
    public function validateCreate(Request $request)
    {
        return $request->validate([
            "code" => "bail|required|max:10|unique:coupons",
            "amount" => "bail|required|integer|min:10000",
            "minimum_spend" => "bail|required|integer|gt:amount",
            "usage" => "bail|required|integer|min:0",
            "per_user" => "bail|required|integer|min:0",
            "start_time" => "bail|required|date|date_format:d-m-Y H:i",
            "end_time" => "bail|required|date|date_format:d-m-Y H:i|after:start_time"
        ]);
    }
    
    public function validateUpdate(Request $request,$id)
    {
        return $request->validate([
            "code" => "bail|required|max:10|unique:coupons,code,$id",
            "amount" => "bail|required|integer|min:10000",
            "minimum_spend" => "bail|required|integer|gt:amount",
            "usage" => "bail|required|integer|min:0",
            "per_user" => "bail|required|integer|min:0",
            "used" => "bail|required|integer|min:0",
            "start_time" => "bail|required|date|date_format:d-m-Y H:i",
            "end_time" => "bail|required|date|date_format:d-m-Y H:i|after:start_time"
        ]);
    }
}