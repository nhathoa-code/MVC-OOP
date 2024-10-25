<?php

namespace NhatHoa\App\Services;
use NhatHoa\Framework\Abstract\Service;
use NhatHoa\App\Models\Category;
use NhatHoa\Framework\Facades\DB;

class CategoryService extends Service
{
    public static function getLastIdFromUrl($string)
    {
        $cats_string = urldecode($string);
        $slugs_arr = explode("/",$cats_string);
        $last_id = 0;
        foreach($slugs_arr as $index => $cat_slug){
            if($index === 0){
                $cat = Category::first(whereNull:array("parent_id"),where:array("cat_slug"=>$cat_slug));
            }else{
                $cat = Category::first(where:array("parent_id"=>$last_id,"cat_slug"=>$cat_slug));
            }
            if($cat){
                $last_id = $cat->id;
            }else{
                return false;
            }
        }
        return $last_id;
    }

    public static function countProducts($category_id)
    {
        return DB::table("product_categories")
                ->where("cat_id",$category_id)
                ->count();
    }
}