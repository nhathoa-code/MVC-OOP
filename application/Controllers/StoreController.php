<?php

namespace NhatHoa\App\Controllers;

use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Store;
use NhatHoa\App\Models\Product;
use NhatHoa\App\Models\Province;
use NhatHoa\App\Middlewares\AdminAuth;
use NhatHoa\Framework\Validation\Rule;
use NhatHoa\Framework\Facades\Excel;
use NhatHoa\Framework\Facades\Gate;

class StoreController extends Controller
{
    protected $storeModel;

    public function __construct(Store $store)
    {
        $this->storeModel = $store;
        $this->middleware(AdminAuth::class)
            ->only([
                "addInventory",
                "updateProductInventory",
                "deleteProductInventory"
            ]);
    }

    public function index(Province $province)
    {
        if(!Gate::allows("read-store")){
            abort(401);
        }
        $stores = $this->storeModel->getList();
        $provinces = array_map(function($item){
            $item->districts = $item->getDistricts();
            return $item;
        },$province->all());
        return view("admin/store/index",array("stores" => $stores,"provinces"=>$provinces));
    }

    public function add(Request $request)
    {
        if(!Gate::allows("create-store")){
            abort(401);
        }
        $validated = $request->validate([
            "name" => "required|unique:stores",
            "address" => "required",
            "coordinates" => "required",
            "province_id" => "bail|required|exists:provinces,id",
            "district_id" => [
                "bail",
                "required",
                Rule::exists("province_districts","id")->where(function($query) use($request){
                    return $query->where("province_id",$request->post("province_id"));
                })
            ]
        ]);
        $this->storeModel->saveStore($validated);
        return response()->back()->with("success","Thêm cửa hàng thành công");
    }

    public function edit($id,Province $province)
    {
        $store = $this->storeModel->getStore($id);
        if(!$store){
            return;
        }
        $provinces = array_map(function($item){
            $item->districts = $item->getDistricts();
            return $item;
        },$province->all());
        $districts = array_find($provinces,function($item) use($store){
            return $item->id == $store->province_id;
        })->districts;
        return view("admin/store/edit",array("store"=>$store,"provinces"=>$provinces,"districts"=>$districts));
    }

    public function update(Request $request,$id)
    {
        if(!Gate::allows("update-store")){
            abort(401);
        }
        $validated = $request->validate([
            "name" => "required|unique:stores,name,$id",
            "address" => "required",
            "coordinates" => "required",
            "province_id" => "bail|required|exists:provinces,id",
            "district_id" => [
                "bail",
                "required",
                Rule::exists("province_districts","id")->where(function($query) use($request){
                    return $query->where("province_id",$request->post("province_id"));
                })
            ],
        ]);
        $store = $this->storeModel->first(where:array("id" => $id));
        if(!$store){
            return;
        }
        $store->updateStore($validated);
        return response()->back()->with("success","Sửa cửa hàng thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-store")){
            abort(401);
        }
        $store = $this->storeModel->first(where:array("id"=>$id));
        if(!$store){
            return;
        }
        $store->deleteStore();
        return response()->back()->with("success","Xóa cửa hàng thành công");
    }

    public function findProduct($product_id,Product $product)
    {
        $product = $product->getProduct($product_id);
        if(!$product){
            return response()->json("Không tìm thấy sản phẩm",400);
        }
        return response()->json($product);
    }

    public function addInventoryView($id)
    {
        $store = $this->storeModel->first(where:array("id"=>$id));
        $products_in_store = $store->getProductsIds(all:true);
        if(count($products_in_store) > 0){
            $products = Product::all(whereNotIn:array("id" => $products_in_store));
        }else{
            $products = Product::all();
        }
        return view("admin/store/add_inventory",["store"=>$store,"products"=>$products]);
    }

    public function addInventory(Request $request,$id)
    {
        $validation = array();
        $validation["store"] = "required|exists:stores,id";
        $validation["products"] = 
        [
            "bail",
            "required",
            "array",
            "string",
            "distinct",
            "exists:products,id",
            Rule::unique("inventory","product_id")->where(function($query) use($id){
                return $query->where("store_id",$id);
            })
        ];
        foreach($request->post("products") as $p_id){
            if($request->has("colors_of_product_{$p_id}")){
                $validation["colors_of_product_{$p_id}"] = "array|exists:product_colors,id";
                if(is_array($request->post("colors_of_product_{$p_id}"))){
                    foreach($request->post("colors_of_product_{$p_id}") as $color){
                        if($request->has("sizes_of_color_{$color}")){
                            $validation["sizes_of_color_{$color}"] = "array";
                            $validation["sizes_of_color_{$color}.*"] = "required";
                            if(is_array($request->post("sizes_of_color_{$color}"))){
                                foreach($request->post("sizes_of_color_{$color}") as $size){
                                    $formated_size = str_replace(".","*",$size);
                                    $validation["stock_of_product_{$p_id}_color_{$color}_{$formated_size}"] = "required|integer|min:1";
                                    $validation["price_of_product_{$p_id}_color_{$color}_{$formated_size}"] = "required|integer|min:10000";
                                }
                            }
                        }else{
                            $validation["stock_of_product_{$p_id}_color_{$color}"] = "required|integer|min:1";
                            $validation["price_of_product_{$p_id}_color_{$color}"] = "required|integer|min:10000";
                        }
                    }
                }
            }else if($request->has("sizes_of_product_{$p_id}")){
                $validation["sizes_of_product_{$p_id}"] = "array";
                $validation["sizes_of_product_{$p_id}.*"] = "required";
                if(is_array($request->post("sizes_of_product_{$p_id}"))){
                    foreach($request->post("sizes_of_product_{$p_id}") as $size){
                        $formated_size = str_replace(".","*",$size);
                        $validation["stock_of_product_{$p_id}_size_{$formated_size}"] = "required|integer|min:1";
                        $validation["price_of_product_{$p_id}_size_{$formated_size}"] = "required|integer|min:10000";
                    }
                }
            }else{
                $validation["stock_of_product_{$p_id}"] = "required|integer|min:1";
                $validation["price_of_product_{$p_id}"] = "required|integer|min:10000";
            }
        }
        $validated = $request->validate($validation);
        $store = $this->storeModel->getStore($id);
        $store->saveInventory($validated);
        return response()->flash("success","Thêm kho thành công !")
                    ->json(["back_url"=>url("admin/store/{$store->id}/inventory")]);
    }

    public function inventory(Request $request,$id)
    {
        $store = $this->storeModel->getStore($id);
        if(!$store){
            return response()->redirect("admin/store")->with("error","Cửa hàng không tồn tại");
        }
        $limit = 10;
        $currentPage = $request->has("page") && is_numeric($request->input("page")) ? $request->input("page") : 1;
        if($request->has("keyword") && !empty($request->input("keyword"))){
            $keyword = $request->input("keyword");
        }else{
            $keyword = null;
        }
        list($inventory,$number_of_products) = $store->getInventory($currentPage, $limit, $keyword);
        $totalPages = ceil($number_of_products / $limit);
        return view("admin/store/inventory",["store"=>$store,"inventory"=>$inventory,"number_of_products"=>$number_of_products,"totalPages"=>$totalPages,"currentPage"=>$currentPage]);
    }

    public function productInventory($id,$product_id)
    {
        $store = Store::first(where:array("id"=>$id));
        if(!$store){
            return;
        }
        $product = $store->getInventoryProduct($product_id);
        return view("admin/store/inventory_product_edit",["store"=>$store,"product"=>$product]);
    }

    public function updateProductInventory(Request $request,$id,$product_id)
    {
        $validation = array();
        $validation["store"] = "required|exists:stores,id";
        $validation["product"] = [
            "required",
            Rule::exists("store_products")->where(function($query) use($id,$product_id){
                return $query->where("store_id",$id)->where("product_id",$product_id);
            })
        ];
        if($request->has("colors_of_product_{$product_id}")){
            $validation["colors_of_product_{$product_id}"] = "array|exists:product_colors,id";
            if(is_array($request->post("colors_of_product_{$product_id}"))){
                foreach($request->post("colors_of_product_{$product_id}") as $color){
                    if($request->has("sizes_of_color_{$color}")){
                        $validation["sizes_of_color_{$color}"] = "array";
                        $validation["sizes_of_color_{$color}.*"] = "required";
                        if(is_array($request->post("sizes_of_color_{$color}"))){
                            foreach($request->post("sizes_of_color_{$color}") as $size){
                                $formated_size = str_replace(".","*",$size);
                                $validation["stock_of_product_{$product_id}_color_{$color}_{$formated_size}"] = "required|integer|min:0";
                            }
                        }
                    }else{
                        $validation["stock_of_product_{$product_id}_color_{$color}"] = "required|integer|min:0";
                    }
                }
            }
        }else if($request->has("sizes_of_product_{$product_id}")){
            $validation["sizes_of_product_{$product_id}"] = "array";
            $validation["sizes_of_product_{$product_id}.*"] = "required";
            if(is_array($request->post("sizes_of_product_{$product_id}"))){
                foreach($request->post("sizes_of_product_{$product_id}") as $size){
                    $formated_size = str_replace(".","*",$size);
                    $validation["stock_of_product_{$product_id}_size_{$formated_size}"] = "required|integer|min:0";
                }
            }
        }else{
            $validation["stock_of_product_{$product_id}"] = "required|integer|min:0";
        }
        $validated = $request->validate($validation);
        $store = $this->storeModel->getStore($id);
        $store->updateInventory($validated,$product_id);
        return response()->flash("success","Cập nhật kho thành công !")
                ->json(["back_url"=>url("admin/store/{$store->id}/inventory")]);
    }

    public function deleteProductInventory($id,$product_id)
    {
        $store = $this->storeModel->getStore($id);
        if($store){
            $store->deleteInventory($product_id);
            return response()->back()->with("success","Đã xóa sản phẩm khỏi cửa hàng");
        }else{
            return response()->back()->with("error","Cửa hàng không tồn tại");
        } 
    }

    public function exportExcel(Request $request,$id)
    {
        $limit = 10;
        $currentPage = $request->has("page") && is_numeric($request->input("page")) ? $request->input("page") : 1;
        if($request->has("keyword") && !empty($request->input("keyword"))){
            $keyword = $request->input("keyword");
        }else{
            $keyword = null;
        }
        $store = $this->storeModel->getStore($id);
        if(!$store){
            return response()->redirect("admin/store")->with("error","Cửa hàng không tồn tại");
        }
        list($products) = $store->getInventory($currentPage, $limit, $keyword);
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