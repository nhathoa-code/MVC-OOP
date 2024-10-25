<?php

namespace NhatHoa\App\Validations;
use NhatHoa\Framework\core\Request;

class ClientValidation
{
    public function validateCheckout(Request $request)
    {
        return $request->validate([
            "name" => "required",
            "email" => "bail|required|email",
            "phone" => "bail|required|regex:/^0[0-9]{9}$/",
            "province" => "required",
            "district" => "required",
            "ward" => "required",
            "address" => "required",
            "payment_method" => "bail|required|in:cod,vnpay",
            "shipping_fee" => "bail|required|numeric",
            "v_point" => "bail|nullable|required|integer|min:1"
        ],[
            "phone.regex" => "Điện thoại không hợp lệ"
        ]);
    }
}