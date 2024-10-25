<?php

namespace NhatHoa\App\Controllers;

use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Product;
use NhatHoa\App\Models\SizeChart;
use NhatHoa\App\Repositories\Interfaces\CategoryRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\ProductRepositoryInterface;
use NhatHoa\App\Services\InventoryService;
use NhatHoa\Framework\Facades\Excel;
use NhatHoa\Framework\Facades\Gate;
use NhatHoa\App\Validations\ProductValidation;

class ProductController extends Controller
{
    protected $productRepository;
    protected $categoryRepository;
    protected $limit = 10;

    public function __construct(ProductRepositoryInterface $productRepository, CategoryRepositoryInterface $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function index(Request $request)
    {
        if(!Gate::allows("read-product")) abort(401);
        $currentPage = max((int) $request->query("page"),1);
        $keyword = $request->query("keyword"); 
        list($products,$number_of_products) = $this->productRepository->getAll($currentPage, $this->limit, $keyword);
        $totalPages = ceil($number_of_products / $this->limit);
        return view("admin/product/index",["products" => $products,"totalPages" => $totalPages, "currentPage" => $currentPage]);
    }

    public function addView()
    {
        $categories = $this->categoryRepository->fetchAll();
        $size_charts = SizeChart::all(orderBy:array("id"=>"desc"));
        return view("admin/product/add",["categories" => $categories,"size_charts"=>$size_charts]);
    }

    public function add(Request $request,ProductValidation $productValidation)
    {
        if(!Gate::allows("create-product")) abort(401);
        $validated = $productValidation->validateCreate($request);
        $this->productRepository->create($validated,$request);
        return response()->flash("message","Thêm sản phẩm thành công")
                        ->json(["back" => url("admin/product")]);
    }

    public function edit($id)
    {
        $product= $this->productRepository->getById($id);
        if(!$product) return;
        $categories = $this->categoryRepository->fetchAll();
        $size_charts = SizeChart::all(orderBy:array("id"=>"desc"));
        return view("admin/product/edit",["product"=>$product,"categories"=>$categories,"size_charts"=>$size_charts]);
    }

    public function update(Request $request,$id,InventoryService $inventoryService,ProductValidation $productValidation)
    {
        if(!Gate::allows("update-product")) abort(401);
        $product = Product::first(where:array("id" => $id));
        if(!$product) return;
        $validated = $productValidation->validateUpdate($request,$id);
        $this->productRepository->update($product,$validated,$request,$inventoryService);
        return response()->flash("message","Cập nhật sản phẩm thành công");
    }

    public function delete(Request $request,$id)
    {
        if(!Gate::allows("delete-product")) abort(401);
        $product = Product::first(where:array("id" => $id));
        if(!$product) return;
        $this->productRepository->delete($product);
        if($request->isAjax()){
            return response()->flash("message","Xóa sản phẩm thành công")
                        ->json(["back"=>url("admin/product")]);
        }else{
            return response()->back()->with("message","Xóa sản phẩm thành công");
        }
    }

    public function exportExcel(Request $request)
    {
        $currentPage = max((int) $request->query("page"),1);
        $keyword = $request->query("keyword"); 
        list($products) = $this->productRepository->getAll($currentPage, $this->limit, $keyword);
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