<?php

namespace NhatHoa\App\Validations;
use NhatHoa\Framework\core\Request;
use NhatHoa\Framework\Validation\Rule;

class StoreValidation
{
    public function validateCreate(Request $request)
    {
        return $request->validate([
            "name" => "required|unique:stores",
            "address" => "required",
            "coordinates" => "required",
            "province_id" => "bail|required|exists:provinces,id",
            "district_id" => [
                "bail",
                "required",
                Rule::exists("province_districts","id")->where(function($query) use($request){
                    return $query->where("province_id",$request->post("province_id"));
                })
            ]
        ]);
    }

    public function validateUpdate(Request $request,int $id)
    {
        return $request->validate([
            "name" => "required|unique:stores,name,$id",
            "address" => "required",
            "coordinates" => "required",
            "province_id" => "bail|required|exists:provinces,id",
            "district_id" => [
                "bail",
                "required",
                Rule::exists("province_districts","id")->where(function($query) use($request){
                    return $query->where("province_id",$request->post("province_id"));
                })
            ],
        ]);
    }

    public function validateAddInventory(Request $request, int $id)
    {
        $validation = array();
        $validation["store"] = "required|exists:stores,id";
        $validation["products"] = 
        [
            "bail",
            "required",
            "array",
            "string",
            "distinct",
            "exists:products,id",
            Rule::unique("inventory","product_id")->where(function($query) use($id){
                return $query->where("store_id",$id);
            })
        ];
        if(is_array($request->post("products"))){
            foreach($request->post("products") as $p_id){
                if($request->has("colors_of_product_{$p_id}")){
                    $validation["colors_of_product_{$p_id}"] = "array|exists:product_colors,id";
                    if(is_array($request->post("colors_of_product_{$p_id}"))){
                        foreach($request->post("colors_of_product_{$p_id}") as $color){
                            if($request->has("sizes_of_color_{$color}")){
                                $validation["sizes_of_color_{$color}"] = "array";
                                $validation["sizes_of_color_{$color}.*"] = "required";
                                if(is_array($request->post("sizes_of_color_{$color}"))){
                                    foreach($request->post("sizes_of_color_{$color}") as $size){
                                        $formated_size = str_replace(".","*",$size);
                                        $validation["stock_of_product_{$p_id}_color_{$color}_{$formated_size}"] = "required|integer|min:1";
                                        $validation["price_of_product_{$p_id}_color_{$color}_{$formated_size}"] = "required|integer|min:10000";
                                    }
                                }
                            }else{
                                $validation["stock_of_product_{$p_id}_color_{$color}"] = "required|integer|min:1";
                                $validation["price_of_product_{$p_id}_color_{$color}"] = "required|integer|min:10000";
                            }
                        }
                    }
                }else if($request->has("sizes_of_product_{$p_id}")){
                    $validation["sizes_of_product_{$p_id}"] = "array";
                    $validation["sizes_of_product_{$p_id}.*"] = "required";
                    if(is_array($request->post("sizes_of_product_{$p_id}"))){
                        foreach($request->post("sizes_of_product_{$p_id}") as $size){
                            $formated_size = str_replace(".","*",$size);
                            $validation["stock_of_product_{$p_id}_size_{$formated_size}"] = "required|integer|min:1";
                            $validation["price_of_product_{$p_id}_size_{$formated_size}"] = "required|integer|min:10000";
                        }
                    }
                }else{
                    $validation["stock_of_product_{$p_id}"] = "required|integer|min:1";
                    $validation["price_of_product_{$p_id}"] = "required|integer|min:10000";
                }
            }
        }
        return $request->validate($validation);
    }

    public function validateUpdateInventory(Request $request,int $id,string $product_id)
    {
        $validation = array();
        $validation["store"] = "required|exists:stores,id";
        $validation["product"] = [
            "required",
            Rule::exists("store_products")->where(function($query) use($id,$product_id){
                return $query->where("store_id",$id)->where("product_id",$product_id);
            })
        ];
        if($request->has("colors_of_product_{$product_id}")){
            $validation["colors_of_product_{$product_id}"] = "array|exists:product_colors,id";
            if(is_array($request->post("colors_of_product_{$product_id}"))){
                foreach($request->post("colors_of_product_{$product_id}") as $color){
                    if($request->has("sizes_of_color_{$color}")){
                        $validation["sizes_of_color_{$color}"] = "array";
                        $validation["sizes_of_color_{$color}.*"] = "required";
                        if(is_array($request->post("sizes_of_color_{$color}"))){
                            foreach($request->post("sizes_of_color_{$color}") as $size){
                                $formated_size = str_replace(".","*",$size);
                                $validation["stock_of_product_{$product_id}_color_{$color}_{$formated_size}"] = "required|integer|min:0";
                            }
                        }
                    }else{
                        $validation["stock_of_product_{$product_id}_color_{$color}"] = "required|integer|min:0";
                    }
                }
            }
        }else if($request->has("sizes_of_product_{$product_id}")){
            $validation["sizes_of_product_{$product_id}"] = "array";
            $validation["sizes_of_product_{$product_id}.*"] = "required";
            if(is_array($request->post("sizes_of_product_{$product_id}"))){
                foreach($request->post("sizes_of_product_{$product_id}") as $size){
                    $formated_size = str_replace(".","*",$size);
                    $validation["stock_of_product_{$product_id}_size_{$formated_size}"] = "required|integer|min:0";
                }
            }
        }else{
            $validation["stock_of_product_{$product_id}"] = "required|integer|min:0";
        }
        return $request->validate($validation);
    }
}