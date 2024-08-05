<?php

namespace NhatHoa\App\Services;
use NhatHoa\Framework\Abstract\Service;
use NhatHoa\Framework\Registry;
use NhatHoa\App\Models\Store;
use NhatHoa\Framework\Facades\DB;

class InventoryService extends Service
{

    protected $_connector;
    protected $_query;

    public function __construct()
    {
        $this->_connector = Registry::get("database");
        $this->_query = $this->_connector->query();
    }

    public function updateStockFromOrder($color_id,$size,$p_id,$quantity,$action = "+")
    {
        $stock = "stock";
        if($color_id){
            if($size){
                $query = $this->_query->from("product_colors_sizes")
                    ->where("p_id",$p_id)
                    ->where("color_id",$color_id)
                    ->where("size",$size)
                    ->limit(1);
            }else{
                $query = $this->_query->from("product_colors")
                    ->where("p_id",$p_id)
                    ->where("id",$color_id)
                    ->limit(1);
            }
        }elseif($size){
            $query = $this->_query->from("product_sizes")
                ->where("p_id",$p_id)
                ->where("size",$size)
                ->limit(1);                   
        }else{
            $query = $this->_query->from("products")
                ->where("id",$p_id)
                ->limit(1);
            $stock = "p_stock";
        }
        switch($action){
            case "+":
                $query->increment($stock,$quantity);
                break;
            case "-":
                $query->decrement($stock,$quantity);    
        }
        
    }

    public function checkStock($product_id,$product_name,$color_id,$color_name,$size,$quantity)
    {
        if($color_id){
            if($size){
                $stock = $this->_query->from("product_colors_sizes")->select(['stock'])->where("p_id",$product_id)->where("color_id",$color_id)->where("size",$size)->getValue("stock");
            }else{
                $stock = $this->_query->from("product_colors")->select(['stock'])->where("p_id",$product_id)->where("id",$color_id)->getValue("stock");
            }
            if($stock === null){
                return array("status" => false,"message" => "Phân loại sản phẩm không đúng!");
            }
            if($quantity > $stock){
                $message = "Sản phẩm {$product_name} - {$color_name}";
                if($size){
                    $message .= " | {$size}";
                }
                if($stock > 0){                   
                    $message .= " chỉ còn {$stock} sản phẩm trong kho!";                  
                }else{
                    $message .= " đã hết hàng!";
                }
                return array("status" => false,"message" => $message);
            }              
        }elseif($size){
            $stock = $this->_query->from("product_sizes")->select(['stock'])->where("p_id",$product_id)->where("size",$size)->getValue("stock");
            if($stock === null){
                return array("status" => false,"message" => "Phân loại sản phẩm không đúng!");
            }
            if($quantity > $stock){
                if($stock > 0){
                    return array("status" => false,"message" => "Sản phẩm {$product_name} - {$size} chỉ còn {$stock} sản phẩm trong kho!");
                }else{
                    return array("status" => false,"message" => "Sản phẩm {$product_name} - {$size} đã hết hàng!");
                }
            }             
        }else{
            $stock = $this->_query->from("products")->select(['p_stock'])->where("id",$product_id)->getValue("p_stock");
            if($stock === null){
                return array("status" => false,"message" => "Sản phẩm không tồn tại!");
            }
            if($quantity > $stock){
                if($stock > 0){
                    return array("status" => false,"message" => "Sản phẩm {$product_name} chỉ còn {$stock} sản phẩm trong kho!");
                }else{
                    return array("status" => false,"message" => "Sản phẩm {$product_name} đã hết hàng!");
                }
            }           
        }
        return array("status" => true, "stock" => $stock);
    }

    public function getOutOfStock($threshold)
    {
        return $this->_query->from("products as p")
                            ->leftJoin("product_colors as pcl","pcl.p_id","=","p.id")
                            ->leftJoin("product_sizes as ps","ps.p_id","=","p.id")
                            ->leftJoin("product_colors_sizes as pcs","pcs.color_id","=","pcl.id")
                            ->select(["p.id","p.p_name","p.p_stock as p_stock","pcl.stock as pcl_stock","pcl.color_name","ps.stock as ps_stock","ps.size as ps_size","pcs.stock as pcs_stock","pcs.size as pcs_size"])->whereCase(function($query) use ($threshold){
                                $query->whenNull("ps.stock")->whenNotNull("pcs.stock")->then("pcs.stock","<",$threshold);
                                $query->whenNull("ps.stock")->whenNull("pcs.stock")->then("pcl.stock","<",$threshold);
                                $query->whenNull("pcl.stock")->whenNull("pcs.stock")->then("ps.stock","<",$threshold);
                                $query->whenElse("p.p_stock","<",$threshold);
                            })
                            ->orderBy("p.created_at","desc")
                            ->get();
    }

    public function checkStockInStore($store_id,$product_id,$color_id,$size,$quantity,$product_name,$color_name)
    {
        if($color_id){
            if($size){
                $stock = $this->_query->from("inventory")->select(['stock'])->where("store_id",$store_id)->where("product_id",$product_id)->where("color_id",$color_id)->where("size",$size)->getValue("stock");
            }else{
                $stock = $this->_query->from("inventory")->select(['stock'])->where("store_id",$store_id)->where("product_id",$product_id)->where("color_id",$color_id)->getValue("stock");
            }
            if($stock === null){
                return array("status" => false,"message" => "Sản phẩm không tồn tại trong cửa hàng!");
            }
            if($quantity > $stock){
                $message = "Sản phẩm {$product_name} - {$color_name}";
                if($size){
                    $message .= " | {$size}";
                }
                if($stock > 0){                   
                    $message .= " chỉ còn {$stock} sản phẩm trong kho!";                  
                }else{
                    $message .= " đã hết hàng!";
                }
                return array("status" => false,"message" => $message);
            }              
        }elseif($size){
            $stock = $this->_query->from("inventory")->select(['stock'])->where("store_id",$store_id)->where("product_id",$product_id)->where("size",$size)->getValue("stock");
            if($stock === null){
                return array("status" => false,"message" => "Sản phẩm không tồn tại trong cửa hàng!");
            }
            if($quantity > $stock){
                if($stock > 0){
                    return array("status" => false,"message" => "Sản phẩm {$product_name} - {$size} chỉ còn {$stock} sản phẩm trong kho!");
                }else{
                    return array("status" => false,"message" => "Sản phẩm {$product_name} - {$size} đã hết hàng!");
                }
            }             
        }else{
            $stock = $this->_query->from("inventory")->select(['stock'])->where("store_id",$store_id)->where("product_id",$product_id)->getValue("stock");
            if($stock === null){
                return array("status" => false,"message" => "Sản phẩm không tồn tại trong cửa hàng!");
            }
            if($quantity > $stock){
                if($stock > 0){
                    return array("status" => false,"message" => "Sản phẩm {$product_name} chỉ còn {$stock} sản phẩm trong kho!");
                }else{
                    return array("status" => false,"message" => "Sản phẩm {$product_name} đã hết hàng!");
                }
            }           
        }
        return array("status" => true, "stock" => $stock);
    }

    public function updateStockInStore($store_id,$product_id,$color_id,$size,$quantity)
    {
        $query = $this->_query->from("inventory")
                        ->where("store_id",$store_id)
                        ->where("product_id",$product_id);
        if($color_id){
            $query->where("color_id",$color_id);
        }
        if($size){
            $query->where("size",$size);
        }
        $query->decrement("stock",$quantity);
    }

    public function updateProductInventoryInStores($product,array $options = [])
    {
        $stores = Store::all();
        foreach($stores as $t){
            if($t->productExistsInStore($product->id)){
                if(isset($options["type"])){
                    if($options["type"] == "color_size"){
                        if(isset($options["action"])){
                            if(isset($options["size"]) && isset($options["color"])){
                                if($options["action"] == "insert"){
                                    DB::table("inventory")
                                        ->where("store_id",$t->id)
                                        ->where("product_id",$product->id)
                                        ->where("color_id",$options["color"])
                                        ->whereNull("size")
                                        ->limit(1)->delete();
                                    DB::table("inventory")->insert([
                                        "store_id" => $t->id,
                                        "product_id" => $product->id,
                                        "color_id" => $options["color"],
                                        "size" => $options["size"],
                                        "price" => $options["price"],
                                        "stock" => $options["stock"]
                                    ]);
                                }
                                if($options["action"] == "delete"){
                                    DB::table("inventory")
                                        ->where("store_id",$t->id)
                                        ->where("product_id",$product->id)
                                        ->where("color_id",$options["color"])
                                        ->where("size",$options["size"])
                                        ->limit(1)->delete();
                                }
                            }
                            if($options["action"] == "delete-all-size"){
                                DB::table("inventory")
                                    ->where("store_id",$t->id)
                                    ->where("product_id",$product->id)
                                    ->where("color_id",$options["color"])
                                    ->delete();
                                DB::table("inventory")
                                    ->insert([
                                        "store_id" => $t->id,
                                        "product_id" => $product->id,
                                        "color_id" => $options["color"],
                                        "price" => $options["price"],
                                        "stock" => $options["stock"]
                                    ]);
                            }
                        }
                    }
                    if($options["type"] == "color"){
                        if(isset($options["action"])){
                            if($options["action"] == "delete"){
                                if(isset($otpions["color"])){
                                    DB::table("inventory")
                                        ->where("store_id",$t->id)
                                        ->where("product_id",$product->id)
                                        ->where("color_id",$options["color"])
                                        ->limit(1)->delete();
                                }
                            }
                        }
                    }
                    if($options["type"] == "size"){
                        if(isset($options["action"])){
                            if(isset($options["size"])){
                                if($options["action"] == "insert"){
                                    DB::table("inventory")->insert([
                                        "store_id" => $t->id,
                                        "product_id" => $product->id,
                                        "size" => $options["size"],
                                        "price" => $options["price"],
                                        "stock" => $options["stock"]
                                    ]);
                                }
                                if($options["action"] == "delete"){
                                    DB::table("inventory")
                                        ->where("store_id",$t->id)
                                        ->where("product_id",$product->id)
                                        ->whereNull("color_id")
                                        ->where("size",$options["size"])
                                        ->limit(1)->delete();
                                }
                            }else{
                                if($options["action"] == "delete" && isset($options["all"])){
                                    DB::table("inventory")
                                        ->where("store_id",$t->id)
                                        ->where("product_id",$product->id)
                                        ->whereNull("color_id")
                                        ->whereNotNull("size")
                                        ->delete();
                                }
                            }
                        }
                    }
                }
                if(isset($options["variant"]) && $options["variant"] == false){
                    DB::table("inventory")
                        ->insert([
                            "store_id" => $t->id,
                            "product_id" => $product->id,
                            "price" => $product->p_price,
                            "stock" => $product->p_stock
                        ]);
                }else{
                    DB::table("inventory")
                        ->where("store_id",$t->id)
                        ->where("product_id",$product->id)
                        ->whereNull("color_id")
                        ->whereNull("size")
                        ->delete();
                }
            }
        }
    }
}