<?php

namespace NhatHoa\App\Validations;
use NhatHoa\Framework\core\Request;

class PosValidation
{
    public function validateCreate(Request $request)
    {
        return $request->validate([
            "store" => "required|exists:stores,id",
            "products" => "required|array|exists:store_products,product_id",
            "prices" => "required|array|arraySameLength:products|integer|min:10000",
            "quantities" => "required|array|arraySameLength:products|integer|min:1",
            "employee" => "required|exists:users,id",
            "customer" => "nullable|integer|exists:customers,id",
            "customer_name" => "bail|required_without:customer|string|min:3",
            "customer_phone" => "bail|required_without:customer|regex:/^0[0-9]{9}$/|unique:customers,phone",
            "total_amount" => "required|integer"
        ]);
    }
}