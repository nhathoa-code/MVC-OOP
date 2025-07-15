<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Repositories\Interfaces\CategoryRepositoryInterface;
use NhatHoa\App\Validations\CategoryValidation;
use NhatHoa\Framework\Facades\Gate;

class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        if(!Gate::allows("read-category")) abort(401);
        $categories = $this->categoryRepository->getAll(null);
        return view("admin/category/index", array("categories" => $categories));
    }

    public function add(Request $request,CategoryValidation $categoryValidation)
    {
        if(!Gate::allows("create-category")) abort(401); 
        $validated = $categoryValidation->validateCreate($request);
        $this->categoryRepository->create($validated,$request);
        return response()->back()->with("success","Thêm danh mục thành công");
    }

    public function edit($id)
    {
        $category = $this->categoryRepository->getById($id);
        if(!$category) return;
        $categories = $this->categoryRepository->getAll(null);
        return view("admin/category/edit",["category" => $category,"categories" => $categories]);
    }

    public function update(Request $request, $id, CategoryValidation $categoryValidation)
    {
        if(!Gate::allows("update-category")) abort(401);
        $validated = $categoryValidation->validateUpdate($request,$id);
        $category = $this->categoryRepository->getById($id);
        if(!$category) return;
        $this->categoryRepository->update($category,$validated,$request);
        return response()->back()->with("success","Cập nhật danh mục thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-category")) abort(401);
        $category = $this->categoryRepository->getById($id);
        if(!$category) return;
        $this->categoryRepository->delete($category);
        return response()->back()->with("success","Xóa danh mục thành công");
    }
}