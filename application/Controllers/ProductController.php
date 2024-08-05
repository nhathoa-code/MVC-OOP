<?php

namespace NhatHoa\App\Controllers;

use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Product;
use NhatHoa\App\Models\Category;
use NhatHoa\App\Models\SizeChart;
use NhatHoa\App\Middlewares\AdminAuth;
use NhatHoa\App\Services\InventoryService;
use NhatHoa\Framework\Facades\Excel;
use NhatHoa\Framework\Facades\Gate;

class ProductController extends Controller
{
    protected $productModel;
    protected $categoryModel;
    protected $sizeChartModel;

    public function __construct(Product $product, Category $category, SizeChart $size_chart)
    {
        $this->productModel = $product;
        $this->categoryModel = $category;
        $this->sizeChartModel = $size_chart;
        $this->middleware(AdminAuth::class)->only(["add","update","delete"]);
    }

    public function index(Request $request)
    {
        if(!Gate::allows("read-product")){
            return "Bạn không có quyền này!";
        }
        $limit = 10;
        $currentPage = $request->has("page") && is_numeric($request->input("page")) ? $request->input("page") : 1;
        if($request->has("keyword") && !empty($request->input("keyword"))){
            $keyword = $request->input("keyword");
        }else{
            $keyword = null;
        }
        list($products,$number_of_products) = $this->productModel->getList($currentPage, $limit, $keyword);
        $totalPages = ceil($number_of_products / $limit);
        return view("admin/product/index",["products" => $products,"totalPages" => $totalPages, "currentPage" => $currentPage]);
    }

    public function addView()
    {
        $categories = $this->categoryModel->fetchCategories();
        $size_charts = $this->sizeChartModel->all(orderBy:array("id"=>"desc"));
        return view("admin/product/add",["categories" => $categories,"size_charts"=>$size_charts]);
    }

    public function add(Request $request)
    {
        if(!Gate::allows("create-product")){
            return "Bạn không có quyền này!";
        }
        $validation_rules = array(
            "p_name" => "required|string|min:5|unique:products",
            "p_price" => "nullable|integer|min:1000",
            "p_stock" => "nullable|integer|min:1",
            "p_images" => "required|image|mimes:png,jpg,webp",
            "p_desc" => "required|string|min:100",
            "size_chart" => "nullable|integer|exists:size_charts,id",
            "categories" => "required|exists:categories,id"
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
        $validated = $request->validate($validation_rules); 
        $this->productModel->saveProduct($validated,$request);
        return response()->flash("message","Thêm sản phẩm thành công")
                        ->json(["back" => url("admin/product")]);
    }

    public function edit($id)
    {
        $product= $this->productModel->getProduct($id);
        if(!$product){
            return;
        }
        $categories = $this->categoryModel->fetchCategories();
        $size_charts = $this->sizeChartModel->all(orderBy:array("id"=>"desc"));
        return view("admin/product/edit",["product"=>$product,"categories"=>$categories,"size_charts"=>$size_charts]);
    }

    public function update(Request $request,$id,InventoryService $inventoryService)
    {
        if(!Gate::allows("update-product")){
            return "Bạn không có quyền này!";
        }
        $validation_rules = array(
            "p_name" => "required|string|min:5|unique:products,p_name,$id",
            "p_price" => "nullable|integer|min:1000",
            "p_stock" => "nullable|integer|min:1",
            "p_images" => "required|image|mimes:png,jpg,webp",
            "p_desc" => "required|string|min:100",
            "size_chart" => "nullable|integer|exists:size_charts,id"
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
        $validated = $request->validate($validation_rules); 
        $product = $this->productModel->first(where:array("id" => $id));
        if(!$product){
            return;
        }
        $product->updateProduct($validated,$request,$inventoryService);
        return response()->flash("message","Cập nhật sản phẩm thành công");
    }

    public function delete(Request $request,$id)
    {
        if(!Gate::allows("delete-product")){
            return "Bạn không có quyền này!";
        }
        $product = Product::first(where:array("id" => $id));
        if(!$product){
            return;
        }
        $product->deleteProduct();
        if($request->isAjax()){
            return response()->flash("message","Xóa sản phẩm thành công")
                        ->json(["back"=>url("admin/product")]);
        }else{
            return response()->back()->with("message","Xóa sản phẩm thành công");
        }
    }

    public function exportExcel(Request $request)
    {
        $limit = 10;
        $currentPage = $request->has("page") && is_numeric($request->input("page")) ? $request->input("page") : 1;
        if($request->has("keyword") && !empty($request->input("keyword"))){
            $keyword = $request->input("keyword");
        }else{
            $keyword = null;
        }
        list($products) = $this->productModel->getList($currentPage, $limit, $keyword);
        $data = array();
        foreach($products as $p){
            if(isset($p->colors_sizes)){
                foreach($p->colors_sizes as $item){
                    $arr = array($p->p_name,$item->color_name . "," . $item->size,$item->price,$item->stock);
                    $data[] = $arr;
                }
            }elseif(isset($p->colors)){
                foreach($p->colors as $item){
                    $arr = array($p->p_name,$item->color_name,$item->price,$item->stock);
                    $data[] = $arr;
                }
            }elseif(isset($p->sizes)){
                foreach($p->sizes as $item){
                    $arr = array($p->p_name,$item->size,$item->price,$item->stock);
                    $data[] = $arr;
                }
            }else{
                $arr = array($p->p_name,null,$p->p_price,$p->p_stock);
                $data[] = $arr;
            }
        }
        $headers = ["Sản phẩm", "Phân loại", "Giá" , "Kho"];
        $autoSizeColumns = ["A","B"];
        $styleHeader = "A1:D1";
        return Excel::generate($data,"products",$headers,$autoSizeColumns,$styleHeader);
    }
}