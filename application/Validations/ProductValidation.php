<?php

namespace NhatHoa\App\Validations;
use NhatHoa\Framework\Core\Request;

class ProductValidation
{
    public function validateCreate(Request $request)
    {
        $validation_rules = array(
            "p_name" => "required|string|min:5|unique:products",
            "p_price" => "nullable|integer|min:1000",
            "p_stock" => "nullable|integer|min:1",
            "p_images" => "required|image|mimes:png,jpg,webp",
            "p_desc" => "required|string|min:100",
            "size_chart" => "nullable|integer|exists:size_charts,id",
            "categories" => "required|exists:categories,id",
            "attr_values" => "nullable|array|exists:attribute_values,id"
        );
        if($request->hasFile("colors")){
            $validation_rules["colors"] = "required|image|mimes:jpg,png,webp";
            foreach($request->file("colors") as $color_index => $file){
                $validation_rules["name_of_color_{$color_index}"] = "required";
                $validation_rules["gallery_of_color_{$color_index}"] = "required|image|mimes:jpg,png,webp";
                if($request->has("sizes_of_color_{$color_index}")){
                    $validation_rules["sizes_of_color_{$color_index}"] = "array";
                    if(is_array($request->input("sizes_of_color_{$color_index}"))){
                        foreach($request->input("sizes_of_color_{$color_index}") as $index => $item){
                            $validation_rules["sizes_of_color_{$color_index}.{$index}.size"] = "required";
                            $validation_rules["sizes_of_color_{$color_index}.{$index}.price"] = "required|numeric|min:1000";
                            $validation_rules["sizes_of_color_{$color_index}.{$index}.stock"] = "required|numeric|min:1";
                        }
                    }
                }
            }
        }else if($request->has("sizes")){
            $validation_rules["sizes"] = "array";
            if(is_array($request->input("sizes"))){
                foreach($request->input("sizes") as $index => $size){
                    $validation_rules["sizes.{$index}.value"] = "required";
                    $validation_rules["sizes.{$index}.price"] = "required|numeric|min:1000";
                    $validation_rules["sizes.{$index}.stock"] = "required|numeric|min:1";
                }
            }
        }else{
            $validation_rules["p_stock"] = "required|numeric|min:1";
        }

        return $request->validate($validation_rules); 
    }

    public function validateUpdate(Request $request,$id)
    {
        $validation_rules = array(
            "p_name" => "required|string|min:5|unique:products,p_name,$id",
            "p_price" => "nullable|integer|min:1000",
            "p_stock" => "nullable|integer|min:1",
            "p_images" => "required|image|mimes:png,jpg,webp",
            "p_desc" => "required|string|min:100",
            "size_chart" => "nullable|integer|exists:size_charts,id",
            "categories" => "required|exists:categories,id",
            "attr_values" => "nullable|array|exists:attribute_values,id"
        );
        if($request->hasFile("colors")){
            $validation_rules["colors"] = "required|image|mimes:jpg,png,webp";
            foreach($request->file("colors") as $color_index => $file){
                $validation_rules["name_of_color_{$color_index}"] = "required";
                $validation_rules["gallery_of_color_{$color_index}"] = "required|image|mimes:jpg,png,webp";
                if($request->has("sizes_of_color_{$color_index}")){
                    $validation_rules["sizes_of_color_{$color_index}"] = "array";
                    if(is_array($request->input("sizes_of_color_{$color_index}"))){
                        foreach($request->input("sizes_of_color_{$color_index}") as $index => $item){
                            $validation_rules["sizes_of_color_{$color_index}.{$index}.size"] = "required";
                            $validation_rules["sizes_of_color_{$color_index}.{$index}.price"] = "required|numeric|min:1000";
                            $validation_rules["sizes_of_color_{$color_index}.{$index}.stock"] = "required|numeric|min:0";
                        }
                    }
                }
            }
        }else if($request->has("sizes")){
            $validation_rules["sizes"] = "array";
            if(is_array($request->input("sizes"))){
                foreach($request->input("sizes") as $index => $size){
                    $validation_rules["sizes.{$index}.value"] = "required";
                    $validation_rules["sizes.{$index}.price"] = "required|numeric|min:1000";
                    $validation_rules["sizes.{$index}.stock"] = "required|numeric|min:0";
                }
            }
        }else{
            $validation_rules["p_stock"] = "required|numeric|min:0";
        }
        return $request->validate($validation_rules); 
    }
}