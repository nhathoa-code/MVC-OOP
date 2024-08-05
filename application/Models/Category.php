<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class Category extends Model
{
    protected $_table = 'categories';

    public function saveCat($validated,$request)
    {
        $this->cat_name = $validated['cat_name'];
        $this->cat_slug = $validated['cat_slug'];
        if(!empty($validated["parent_id"])){
            $this->parent_id = $validated['parent_id'];
        }
        if($request->hasFile("cat_image")){
            $this->cat_image = $request->file("cat_image")->save("images/cat");
        }
        $this->save();
    }

    public function updateCat($validated,$request)
    {
        $this->cat_name = $validated['cat_name'];
        $this->cat_slug = $validated['cat_slug'];
        if(!empty($validated["parent_id"])){
            $this->parent_id = $validated['parent_id'];
        }else{
            $this->parent_id = NULL;
        }
        if(isset($validated["delete_cat_image"])){
            delete_file($validated["delete_cat_image"]);
            $this->cat_image = null;
        }
        if($request->hasFile("cat_image")){
            if($this->cat_image){
                delete_file("images/cat/$this->cat_image");
            }
            $this->cat_image = $request->file("cat_image")->save("images/cat");
        }
        $this->save();
    }

    public function deleteCat()
    {
        if($this->cat_image){
            delete_file("images/cat/$this->cat_image");
        }
        $subCategories = $this->all(where:array("parent_id" => $this->id));
        foreach($subCategories as $subCat){
            $subCat->parent_id = $this->parent_id;
            $subCat->save();
        }
        $this->delete();
    }

    public function hasChildren()
    {
        $children = $this->count(where:array("parent_id" => $this->id));
        if($children > 0){
            return true;
        }
        return false;
    }

    public function getChildren()
    {
        return $this->all(where:array("parent_id" => $this->id));
    }

    public function getAll(array $categories = null) 
    {
        $array = array();
        if(!$categories){
            $root_categories = $this->all(whereNull:array("parent_id"));
        }else{
            $root_categories = $categories;
        }
        foreach($root_categories as $cat){
           if($cat->hasChildren()){
                $cat->children = $cat->getAll($cat->getChildren());
           }
           $cat->products = $cat->countProducts();
           $array[] = $cat;
        }
        return $array;
    }

    public function countProducts()
    {
        $count = $this->count(table:"product_categories", where:array("cat_id" => $this->id));
        return $count;
    }

    public function fetchCategories(array $categories = null)
    {
        $categories_arr = array();
        if(!$categories){
            $categories = $this->all(whereNull:array("parent_id"));
        }
        foreach($categories as $cat){
            $category = array(
                "id" => $cat->id,
                "cat_name" => $cat->cat_name,
                "parent_id" => $cat->parent_id
            );
            if($cat->hasChildren()){
                $category["children"] = $cat->fetchCategories($cat->getChildren());
            }
            $categories_arr[] = $category;
        }
        return $categories_arr;
    }

    public function getSiblings($cat_id)
    {
        return $this->all(where:array(array("id"=>$cat_id,"operator"=>"!=")));
    }

    public function getLastIdFromUrl($string)
    {
        $cats_string = urldecode($string);
        $slugs_arr = explode("/",$cats_string);
        $last_id = 0;
        foreach($slugs_arr as $index => $cat_slug){
            if($index === 0){
                $cat = $this->first(whereNull:array("parent_id"),where:array("cat_slug"=>$cat_slug));
            }else{
                $cat = $this->first(where:array("parent_id"=>$last_id,"cat_slug"=>$cat_slug));
            }
            if($cat){
                $last_id = $cat->id;
            }else{
                return false;
            }
        }
        return $last_id;
    }
}

