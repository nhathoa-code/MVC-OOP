<?php

namespace NhatHoa\App\Validations;
use NhatHoa\Framework\core\Request;
use NhatHoa\Framework\Validation\Rule;

class CategoryValidation
{
    public function validateCreate(Request $request)
    {
        return $request->validate([
            "cat_name" => ["bail","required","string","min:3",Rule::unique("categories")->where(function($query) use($request){
                if(!empty($request->input("parent_id"))){
                    $query->where("parent_id",$request->input("parent_id"));
                }else{
                    $query->whereNull("parent_id");
                }
                return $query;
            })],
            "cat_slug" => ["bail","required","string","min:3",Rule::unique("categories")->where(function($query) use($request){
                if(!empty($request->input("parent_id"))){
                    $query->where("parent_id",$request->input("parent_id"));
                }else{
                    $query->whereNull("parent_id");
                }
                return $query;
            })],
            "parent_id" => "bail|nullable|exists:categories,id",
            "cat_image" => "bail|nullable|mimes:png,jpg,webp"
        ]);
    }

    public function validateUpdate(Request $request,int $id)
    {
        return $request->validate([
            "cat_name" => ["bail","required","string","min:3",Rule::unique("categories")->where(function($query) use($request,$id){
                if(!empty($request->input("parent_id"))){
                    $query->where("parent_id",$request->input("parent_id"))->where("id","!=",$id);
                }else{
                    $query->whereNull("parent_id")->where("id","!=",$id);
                }
                return $query;
            })],
            "cat_slug" => ["bail","required","string","min:3",Rule::unique("categories")->where(function($query) use($request,$id){
                if(!empty($request->input("parent_id"))){
                    $query->where("parent_id",$request->input("parent_id"))->where("id","!=",$id);
                }else{
                    $query->whereNull("parent_id")->where("id","!=",$id);
                }
                return $query;
            })],
            "parent_id" => "bail|nullable|exists:categories,id",
            "cat_image" => "bail|nullable|image|mimes:png,jpg,webp"
        ]);
    }
}