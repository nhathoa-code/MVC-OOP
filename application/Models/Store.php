<?php

namespace NhatHoa\App\Models;

use NhatHoa\App\Repositories\Interfaces\ProductRepositoryInterface;
use NhatHoa\Framework\Abstract\Model;
use NhatHoa\App\Services\InventoryService;

class Store extends Model
{
    public function saveInventory($validated)
    {
        foreach($validated["products"] as $p_id){
            $this->table("store_products")
                ->insert([
                    "store_id" => $this->id,
                    "product_id" => $p_id
                ]);
            if(isset($validated["colors_of_product_{$p_id}"])){
                foreach($validated["colors_of_product_{$p_id}"] as $color){
                    if(isset($validated["sizes_of_color_{$color}"])){
                        foreach($validated["sizes_of_color_{$color}"] as $size){
                            $formated_size = str_replace(".","*",$size);
                            $stock = $validated["stock_of_product_{$p_id}_color_{$color}_{$formated_size}"];
                            $price = $validated["price_of_product_{$p_id}_color_{$color}_{$formated_size}"];
                            $this->table("inventory")
                                ->insert([
                                    "product_id" => $p_id,
                                    "color_id" => $color,
                                    "size" => $size,
                                    "store_id" => $this->id,
                                    "stock" => $stock,
                                    "price" => $price
                                ]);
                        }
                    }else{
                        $stock = $validated["stock_of_product_{$p_id}_color_{$color}"];
                        $price = $validated["price_of_product_{$p_id}_color_{$color}"];
                        $this->table("inventory")
                            ->insert([
                                "product_id" => $p_id,
                                "color_id" => $color,
                                "store_id" => $this->id,
                                "stock" => $stock,
                                "price" => $price
                            ]);
                    }
                }
            }else if(isset($validated["sizes_of_product_{$p_id}"])){
                foreach($validated["sizes_of_product_{$p_id}"] as $size){
                    $formated_size = str_replace(".","*",$size);
                    $stock = $validated["stock_of_product_{$p_id}_size_{$formated_size}"];
                    $price = $validated["price_of_product_{$p_id}_size_{$formated_size}"];
                    $this->table("inventory")
                        ->insert([
                            "product_id" => $p_id,
                            "size" => $size,
                            "store_id" => $this->id,
                            "stock" => $stock,
                            "price" => $price
                        ]);
                }
            }else{
                $stock = $validated["stock_of_product_{$p_id}"];
                $price = $validated["price_of_product_{$p_id}"];
                $this->table("inventory")
                    ->insert([
                        "product_id" => $p_id,
                        "store_id" => $this->id,
                        "stock" => $stock,
                        "price" => $price
                    ]);
            }
        }
    }

    public function getInventory($currentPage, $limit, $keyword, $all = false,ProductRepositoryInterface $productRepository)
    {
        if($all === false){
            list($products,$number_of_products) = $this->getProducts($currentPage, $limit, $keyword, $all,$productRepository);
        }else{
            $products = $this->getProducts($currentPage, $limit, $keyword, $all,$productRepository);
        }
        foreach($products as $p){
            $p->product_images = getFiles("images/products/{$p->dir}/product_images");
            $query = $this->table("inventory as i")
                        ->where("product_id",$p->id)
                        ->where("store_id",$this->id)
                        ->join("products as p","p.id","=","i.product_id");
            if($p->hasColorsSizes()){
                $p->colors_sizes = $query->select(["p.p_name,p.id,pc.color_name,pc.color_image,i.size,i.stock,i.price"])
                            ->leftJoin("product_colors as pc","pc.id","=","i.color_id")
                            ->get();  
            }else if($p->hasColors()){
                $p->colors = $query->select(["p.p_name,p.id,pc.color_name,pc.color_image,i.size,i.stock,i.price"])
                            ->leftJoin("product_colors as pc","pc.id","=","i.color_id")
                            ->orderBy("i.id","desc")
                            ->get();  
            }else if($p->hasSizes()){   
                $p->sizes = $query->select(["p.p_name,p.id,i.size,i.stock,i.price"])
                            ->get();  
            }else{
                $p->stock = $query->select(["p.p_name,p.id,i.stock,i.price"])
                            ->get();  
            }
        }
        if($all === false){
            return array($products,$number_of_products);
        }else{
            return $products;
        }
    }

    public function getProducts($currentPage = null, $limit = null, $keyword = null, $all = false, ProductRepositoryInterface $productRepository)
    {
        if($all === false){
            list($product_ids,$number_of_products) = $this->getProductsIds($currentPage, $limit, $keyword, $all);
        }else{
            $product_ids = $this->getProductsIds($currentPage, $limit, $keyword, $all);
        }
        $products = array_map(function($item) use($productRepository){
            return $productRepository->getById($item);
        },$product_ids);
        if($all === false){
            return array($products,$number_of_products);
        }else{
            return $products;
        }
    }

    public function getProductsIds($currentPage = null, $limit = null, $keyword = null, $all = false)
    {
        $query = $this->table("store_products as stp")
                    ->where("store_id",$this->id)
                    ->join("products as p","p.id","=","stp.product_id")
                    ->orderBy("stp.id","desc");
        if($all === false){
            if($keyword){
                $query = $query->where(function($query) use($keyword){
                    $query->where("p.p_name","like","%{$keyword}%")
                        ->orWhere("p.id","like","%{$keyword}%");
                });
            }
            $number_of_products = $query->count(false);
            $products_ids = $query->limit($limit)->offset(($currentPage - 1) * $limit)->getArray("product_id");
            return array($products_ids,$number_of_products);
        }else{
            return $query->getArray("product_id");
        }
    }

    public function getInventoryProduct($product_id, ProductRepositoryInterface $productRepository)
    {
        $product = $productRepository->getById($product_id);
        if(!$product) return null;
        $product->inventory = $this->table("inventory")
                                ->where("store_id",$this->id)
                                ->where("product_id",$product->id)
                                ->get();
        return $product;
    }

    public function updateInventory($validated,$product_id)
    {
        if(isset($validated["colors_of_product_{$product_id}"])){
            foreach($validated["colors_of_product_{$product_id}"] as $color){
                if(isset($validated["sizes_of_color_{$color}"])){
                    foreach($validated["sizes_of_color_{$color}"] as $size){
                        $formated_size = str_replace(".","*",$size);
                        $stock = $validated["stock_of_product_{$product_id}_color_{$color}_{$formated_size}"];
                        $price = $validated["price_of_product_{$product_id}_color_{$color}_{$formated_size}"];
                        if($this->checkColorExists($product_id,$color)){
                            $this->table("inventory")
                                ->where("store_id",$this->id)
                                ->where("product_id",$product_id)
                                ->where("product_id",$product_id)
                                ->where("color_id",$color)
                                ->where("size",$size)
                                ->update([
                                    "stock" => $stock,
                                    "price" => $price
                                ]);
                        }else{
                            $this->table("inventory")
                                ->insert([
                                    "product_id" => $product_id,
                                    "color_id" => $color,
                                    "size" => $size,
                                    "store_id" => $this->id,
                                    "stock" => $stock,
                                    "price" => $price
                                ]);
                        }
                    }
                }else{
                    $stock = $validated["stock_of_product_{$product_id}_color_{$color}"];
                    $price = $validated["price_of_product_{$product_id}_color_{$color}"];
                    if($this->checkColorExists($product_id,$color)){
                        $this->table("inventory")
                            ->where("product_id",$product_id)
                            ->where("store_id",$this->id)
                            ->where("color_id",$color)
                            ->update([
                                "stock" => $stock,
                                "price" => $price
                            ]);
                    }else{
                        $this->table("inventory")
                            ->insert([
                                "product_id" => $product_id,
                                "color_id" => $color,
                                "store_id" => $this->id,
                                "stock" => $stock,
                                "price" => $price
                            ]);
                    }
                }
            }
        }else if(isset($validated["sizes_of_product_{$product_id}"])){
            foreach($validated["sizes_of_product_{$product_id}"] as $size){
                $formated_size = str_replace(".","*",$size);
                $stock = $validated["stock_of_product_{$product_id}_size_{$formated_size}"];
                $price = $validated["price_of_product_{$product_id}_size_{$formated_size}"];
                if($this->checkSizeExists($product_id,$size)){
                    $this->table("inventory")
                        ->where("store_id",$this->id)
                        ->where("product_id",$product_id)
                        ->where("size",$size)
                        ->update([
                            "stock" => $stock,
                            "price" => $price
                        ]);
                }else{
                    $this->table("inventory")
                        ->insert([
                            "product_id" => $product_id,
                            "size" => $size,
                            "store_id" => $this->id,
                            "stock" => $stock,
                            "price" => $price
                        ]);
                }
            }
        }else{
            $stock = $validated["stock_of_product_{$product_id}"];
            $price = $validated["price_of_product_{$product_id}"];
            if($this->checkProductExists($product_id)){
                $this->table("inventory")
                    ->where("store_id",$this->id)
                    ->where("product_id",$product_id)
                    ->update([
                        "stock" => $stock,
                        "price" => $price
                    ]);
            }else{
                $this->table("inventory")
                    ->insert([
                        "product_id" => $product_id,
                        "store_id" => $this->id,
                        "stock" => $stock,
                        "price" => $price
                    ]);
            }
        }
    }

    public function deleteInventory($product_id)
    {
        $this->table("inventory")
            ->where("store_id",$this->id)
            ->where("product_id",$product_id)
            ->delete();
        $this->table("store_products")
            ->where("store_id",$this->id)
            ->where("product_id",$product_id)
            ->limit(1)
            ->delete();
    }

    public function checkColorExists($p_id,$color_id)
    {
        return $this->table("inventory")
                    ->where("store_id",$this->id)
                    ->where("product_id",$p_id)
                    ->where("color_id",$color_id)
                    ->first();
    }

    public function checkSizeExists($p_id,$size)
    {
        return $this->table("inventory")
                    ->where("store_id",$this->id)
                    ->where("product_id",$p_id)
                    ->whereNull("color_id")
                    ->where("size",$size)
                    ->first();
    }

    public function checkProductExists($p_id)
    {
        return $this->table("inventory")
                    ->where("store_id",$this->id)
                    ->where("product_id",$p_id)
                    ->whereNull("color_id")
                    ->whereNull("size")
                    ->first();
    }

    public function productExistsInStore($product_id)
    {
        return $this->table("store_products")
                ->where("store_id",$this->id)
                ->where("product_id",$product_id)
                ->first();
    }

    public function getProductStock($product_id)
    {
        return $this->table("inventory")
                    ->where("store_id",$this->id)
                    ->where("product_id",$product_id)
                    ->get();
    }

    public function createSale($validated,InventoryService $inventoryService)
    {
        $products = $validated["products"];
        $prices = $validated["prices"];
        $quantities = $validated["quantities"];
        $bill_id = $this->makeBill($validated["employee"],$validated["customer"],$validated["total_amount"]);
        foreach($products as $index => $p_id){
            $color_id = $validated["colors"][$index] ?? null;
            $size = $validated["sizes"][$index] ?? null;
            $product_name = $validated["product_names"][$index] ?? "";
            $color_name = $validated["color_names"][$index] ?? "";
            $arr = $inventoryService->checkStockInStore($this->id,$p_id,$color_id,$size,$quantities[$index],$product_name,$color_name);
            if($arr["status"] === false){
                return array("status"=>false,"message"=>$arr["message"]);
            }else{
                $this->insertSaleItem($bill_id,$p_id,$color_id,$size,$quantities[$index],$prices[$index]);
                $inventoryService->updateStockInStore($this->id,$p_id,$color_id,$size,$quantities[$index]);
            }
        }
        return array("status"=>true,"bill"=>url("admin/pos/invoice/{$bill_id}/print"));
    }

    public function makeBill($employee,$customer,$total_amount)
    {
        return $this->table("sales")
                ->insert([
                    "store_id" => $this->id,
                    "employee_id" => $employee,
                    "customer_id" => $customer,
                    "total_amount" => $total_amount,
                ]);
    }

    public function insertSaleItem($sale_id,$product_id,$color_id,$size,$quantity,$price)
    {
        $data = array(
            "sale_id" => $sale_id,
            "product_id" => $product_id,
            "quantity" => $quantity,
            "price" => $price
        );
        if($color_id){
            $data["color_id"] = $color_id;
        }
        if($size){
            $data["size"] = $size;
        }
        $this->table("sale_items")->insert($data);
    }
}