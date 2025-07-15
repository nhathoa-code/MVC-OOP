<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\App\Models\Category;
use NhatHoa\Framework\Core\Request;
use NhatHoa\App\Repositories\Interfaces\CategoryRepositoryInterface;
use NhatHoa\App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function getAll(array|null $categories) : array
    {
        $array = array();
        if(!$categories){
            $root_categories = Category::all(whereNull:array("parent_id"));
        }else{
            $root_categories = $categories;
        }
        foreach($root_categories as $cat){
           if($cat->hasChildren()){
                $cat->children = $this->getAll($cat->getChildren());
           }
           $cat->products = $cat->countProducts();
           $array[] = $cat;
        }
        return $array;
    }

    public function getById($id) : Category|null
    {
        return Category::first(where:array("id" => $id));
    }

    public function create($validated,Request $request) : void
    {   
        $category = new Category;
        $category->cat_name = $validated['cat_name'];
        $category->cat_slug = $validated['cat_slug'];
        if(!empty($validated["parent_id"])){
            $category->parent_id = $validated['parent_id'];
        }
        if($request->post("featured")){
            $category->featured = true;
        }
        if($request->hasFile("cat_image")){
            $category->cat_image = $request->file("cat_image")->save("images/cat");
        }
        $category->save();
    }

    public function update(Category $category,$validated,Request $request) : void
    {
        $category->cat_name = $validated['cat_name'];
        $category->cat_slug = $validated['cat_slug'];
        if(!empty($validated["parent_id"])){
            $category->parent_id = $validated['parent_id'];
        }else{
            $category->parent_id = NULL;
        }
        if(isset($validated["delete_cat_image"])){
            delete_file($validated["delete_cat_image"]);
            $category->cat_image = null;
        }
        if($request->post("featured")){
            $category->featured = true;
        }else{
            $category->featured = false;
        }
        if($request->hasFile("cat_image")){
            if($category->cat_image){
                delete_file("images/cat/{$category->cat_image}");
            }
            $category->cat_image = $request->file("cat_image")->save("images/cat");
        }
        $category->save();
    }

    public function delete(Category $category) : void
    {
        if($category->cat_image){
            delete_file("images/cat/$category->cat_image");
        }
        $subCategories = $category->all(where:array("parent_id" => $category->id));
        foreach($subCategories as $subCat){
            $subCat->parent_id = $category->parent_id;
            $subCat->save();
        }
        $category->delete();
    }

    public function fetchAll(array|null $categories = null) : array
    {
        $categories_arr = array();
        if(!$categories){
            $categories = Category::all(whereNull:array("parent_id"));
        }
        foreach($categories as $cat){
            $category = array(
                "id" => $cat->id,
                "cat_name" => $cat->cat_name,
                "parent_id" => $cat->parent_id
            );
            if($cat->hasChildren()){
                $category["children"] = $this->fetchAll($cat->getChildren());
            }
            $categories_arr[] = $category;
        }
        return $categories_arr;
    }
}