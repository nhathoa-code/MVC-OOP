<?php

namespace NhatHoa\App\Controllers;

use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Store;
use NhatHoa\App\Models\Product;
use NhatHoa\App\Models\Province;
use NhatHoa\App\Middlewares\AdminAuth;
use NhatHoa\App\Repositories\Interfaces\ProductRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\StoreRepositoryInterface;
use NhatHoa\App\Validations\StoreValidation;
use NhatHoa\Framework\Facades\Excel;
use NhatHoa\Framework\Facades\Gate;

class StoreController extends Controller
{
    protected $storeRepository;
    protected $limit = 10;

    public function __construct(StoreRepositoryInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository;
        $this->middleware(AdminAuth::class)
            ->only([
                "addInventory",
                "updateProductInventory",
                "deleteProductInventory"
            ]);
    }

    public function index(Province $province)
    {
        if(!Gate::allows("read-store")) abort(401);
        $stores = $this->storeRepository->getAll();
        $provinces = array_map(function($item){
            $item->districts = $item->getDistricts();
            return $item;
        },$province->all());
        return view("admin/store/index",array("stores" => $stores,"provinces"=>$provinces));
    }

    public function add(Request $request,StoreValidation $storeValidation)
    {
        if(!Gate::allows("create-store")) abort(401);
        $validated = $storeValidation->validateCreate($request);
        $this->storeRepository->create($validated);
        return response()->back()->with("success","Thêm cửa hàng thành công");
    }

    public function edit($id,Province $province)
    {
        $store = $this->storeRepository->getById($id);
        if(!$store) return;
        $provinces = array_map(function($item){
            $item->districts = $item->getDistricts();
            return $item;
        },$province->all());
        $districts = array_find($provinces,function($item) use($store){
            return $item->id == $store->province_id;
        })->districts;
        return view("admin/store/edit",array("store"=>$store,"provinces"=>$provinces,"districts"=>$districts));
    }

    public function update(Request $request, $id, StoreValidation $storeValidation)
    {
        if(!Gate::allows("update-store")) abort(401);
        $validated = $storeValidation->validateUpdate($request,$id);
        $store = $this->storeRepository->getById($id);
        if(!$store) return;
        $this->storeRepository->update($store,$validated);
        return response()->back()->with("success","Sửa cửa hàng thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-store")) abort(401);
        $store = $this->storeRepository->getById($id);
        if(!$store) return;
        $this->storeRepository->delete($store);
        return response()->back()->with("success","Xóa cửa hàng thành công");
    }

    public function findProduct($product_id,ProductRepositoryInterface $productRepository)
    {
        $product = $productRepository->getById($product_id);
        if(!$product) return response()->json("Không tìm thấy sản phẩm",400);
        return response()->json($product);
    }

    public function addInventoryView($id)
    {
        $store = $this->storeRepository->getById($id);
        $products_in_store = $store->getProductsIds(all:true);
        if(count($products_in_store) > 0){
            $products = Product::all(whereNotIn:array("id" => $products_in_store));
        }else{
            $products = Product::all();
        }
        return view("admin/store/add_inventory",["store"=>$store,"products"=>$products]);
    }

    public function addInventory(Request $request, $id, StoreValidation $storeValidation)
    {
        $validated = $storeValidation->validateAddInventory($request, $id);
        $store = $this->storeRepository->getById($id);
        if(!$store) return;
        $store->saveInventory($validated);
        return response()->flash("success","Thêm kho thành công !")
                    ->json(["back_url"=>url("admin/store/{$store->id}/inventory")]);
    }

    public function inventory(Request $request,$id,ProductRepositoryInterface $productRepository)
    {
        $store = $this->storeRepository->getById($id);
        if(!$store){
            return response()->redirect("admin/store")->with("error","Cửa hàng không tồn tại");
        }
        $currentPage = max((int) $request->query("page"),1);
        $keyword = $request->query("keyword"); 
        list($inventory,$number_of_products) = $store->getInventory($currentPage, $this->limit, $keyword, false,productRepository:$productRepository);
        $totalPages = ceil($number_of_products / $this->limit);
        return view("admin/store/inventory",["store"=>$store,"inventory"=>$inventory,"number_of_products"=>$number_of_products,"totalPages"=>$totalPages,"currentPage"=>$currentPage]);
    }

    public function productInventory($id,$product_id,ProductRepositoryInterface $productRepository)
    {
        $store = Store::first(where:array("id"=>$id));
        if(!$store) return;
        $product = $store->getInventoryProduct($product_id,$productRepository);
        return view("admin/store/inventory_product_edit",["store"=>$store,"product"=>$product]);
    }

    public function updateProductInventory(Request $request, $id, $product_id, StoreValidation $storeValidation)
    {
        $validated = $storeValidation->validateUpdateInventory($request,$id,$product_id);
        $store = $this->storeRepository->getById($id);
        if(!$store) return;
        $store->updateInventory($validated,$product_id);
        return response()->flash("success","Cập nhật kho thành công !")
                ->json(["back_url"=>url("admin/store/{$store->id}/inventory")]);
    }

    public function deleteProductInventory($id,$product_id)
    {
        $store = $this->storeRepository->getById($id);
        if($store){
            $store->deleteInventory($product_id);
            return response()->back()->with("success","Đã xóa sản phẩm khỏi cửa hàng");
        }else{
            return response()->back()->with("error","Cửa hàng không tồn tại");
        } 
    }

    public function exportExcel(Request $request,$id,ProductRepositoryInterface $productRepository)
    {
        $currentPage = max((int) $request->query("page"),1);
        $keyword = $request->query("keyword"); 
        $store = $this->storeRepository->getById($id);
        if(!$store){
            return response()->redirect("admin/store")->with("error","Cửa hàng không tồn tại");
        }
        list($products) = $store->getInventory($currentPage, $this->limit, $keyword, false,productRepository:$productRepository);
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
        return Excel::generate($data,$store->name,$headers,$autoSizeColumns,$styleHeader);
    }
}