<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Category;
use NhatHoa\App\Middlewares\AdminAuth;
use NhatHoa\Framework\Facades\Gate;

class CategoryController extends Controller
{
    protected $categoryModel;

    public function __construct(Category $category)
    {
        $this->categoryModel = $category;
        $this->middleware(AdminAuth::class)->only(["add","update","delete"]);
    }

    public function index()
    {
        if(!Gate::allows("read-category")){
            abort(401);
        }
        $categories = $this->categoryModel->getAll();
        return view("admin/category/index", array("categories" => $categories));
    }

    public function add(Request $request)
    {
        if(!Gate::allows("create-category")){
            abort(401);
        }
        $validated = $request->validate([
            "cat_name" => "bail|required|string|min:3",
            "cat_slug" => "bail|required|string|min:3",
            "parent_id" => "bail|nullable|exists:categories,id",
            "cat_image" => "bail|nullable|mimes:png,jpg,webp"
        ]);
        $this->categoryModel->saveCat($validated, $request);
        return response()->back()->with("success","Thêm danh mục thành công");
    }

    public function edit($id)
    {
        $category = Category::first(where:array("id" => $id));
        if(!$category){
            return;
        }
        $categories = $this->categoryModel->getAll();
        return view("admin/category/edit",["category" => $category,"categories" => $categories]);
    }

    public function update(Request $request, $id)
    {
        if(!Gate::allows("update-category")){
            abort(401);
        }
        $validated = $request->validate([
            "cat_name" => "bail|required|min:3",
            "cat_slug" => "bail|required|min:3",
            "parent_id" => "bail|nullable|exists:categories,id",
            "cat_image" => "bail|nullable|image|mimes:png,jpg,webp"
        ]);
        $category = $this->categoryModel->first(where:array("id" => $id));
        if(!$category){
            return;
        }
        $category->updateCat($validated, $request);
        return response()->back()->with("success","Cập nhật danh mục thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-category")){
            abort(401);
        }
        $category = Category::first(where:array("id" => $id));
        if(!$category){
            return;
        }
        $category->deleteCat();
        return response()->back()->with("success","Xóa danh mục thành công");
    }
}