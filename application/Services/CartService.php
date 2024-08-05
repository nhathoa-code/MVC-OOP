<?php

namespace NhatHoa\App\Services;
use NhatHoa\Framework\Abstract\Service;
use NhatHoa\Framework\Registry;
use NhatHoa\App\Services\InventoryService;

class CartService extends Service
{
    protected $_session;
    protected $_inventoryService;

    public function __construct()
    {
        $this->_session = Registry::get("session");
        $this->_inventoryService = new InventoryService();
    }

    public function getItems()
    {
        return $this->_session->get("cart");
    }

    public function addItem($validated)
    {
        $product_id = $validated["p_id"];
        $color_id = $validated["color_id"] ?? null;
        $size = $validated["size"] ?? null;
        if($this->_session->has("cart")){
            $matchIndex = $this->_checkItemExisted($product_id,$color_id,$size);
            if ($matchIndex !== false) {
                $array = $this->_inventoryService->checkStock($product_id,$validated["p_name"],$color_id,$validated["color_name"] ?? null,$size,$this->_session->get("cart=>{$matchIndex}=>quantity") + 1);
                if(!$array["status"]){
                    return array("status"=>false,"message"=>$array["message"]);
                }
                $this->_session->set("cart=>{$matchIndex}=>quantity",$this->_session->get("cart=>{$matchIndex}=>quantity") + 1);
                return array("status"=>"old","index"=>$matchIndex,"quantity"=> $this->_session->get("cart=>{$matchIndex}=>quantity"),"totalItems"=>$this->countTotalItems(),"subtotal"=>$this->countSubtotal());
            } else {
                $array = $this->_inventoryService->checkStock($product_id,$validated["p_name"],$color_id,$validated["color_name"] ?? null,$size,1);
                if(!$array["status"]){
                    return array("status"=>false,"message"=>$array["message"]);
                }
                $uniqid = uniqid();
                $item = array(
                    "p_id" => $product_id,
                    "p_name" => $validated["p_name"],
                    "p_image" => $validated["p_image"],
                    "quantity" => 1,
                    "price" => intval($validated["price"])
                );
                if($size){
                    $item['size'] = $size;
                }
                if($color_id){
                    $item['color_id'] = $color_id;
                    $item['color_name'] = $validated["color_name"];
                    $item['color_image'] = $validated["color_image"];
                }
                $this->_session->push("cart",$item,$uniqid);
            }
        }else{
            $array = $this->_inventoryService->checkStock($product_id,$validated["p_name"],$color_id,$validated["color_name"] ?? null,$size,1);
            if(!$array["status"]){
                return array("status"=>false,"message"=>$array["message"]);
            }
            $uniqid = uniqid();
            $item = array(
                "p_id" => $product_id,
                "p_name" => $validated["p_name"],
                "color_id" => $color_id,
                "color_name" => $validated["color_name"] ?? null,
                "color_image" => $validated["color_image"] ?? null,
                "p_image" => $validated["p_image"] ?? null,
                "size" => $size,
                "quantity" => 1,
                "price" => intval($validated["price"])
            );
            $this->_session->push("cart",$item,$uniqid);
        }
        $item["id"] = $uniqid;
        if(isset($item["color_image"])){
            $item["color_image"] = url($item["color_image"]);
        }
        return array(
            "status"=>"new",
            "item"=>$item,
            "totalItems"=>$this->countTotalItems(),
            "subtotal"=>$this->countSubtotal()
        );
    }

    public function updateItem($sign, $index)
    {
        $product_id = $this->_session->get("cart=>{$index}=>p_id");
        $product_name = $this->_session->get("cart=>{$index}=>p_name");
        $color_id = $this->_session->get("cart=>{$index}=>color_id");
        $color_name = $this->_session->get("cart=>{$index}=>color_name");
        $size = $this->_session->get("cart=>{$index}=>size");
        switch($sign){
            case "+":
                $array = $this->_inventoryService->checkStock($product_id,$product_name,$color_id,$color_name,$size,$this->_session->get("cart=>{$index}=>quantity") + 1);
                if(!$array["status"]){
                    return array("status"=>false,"message"=>$array["message"]);
                }
                $this->_session->set("cart=>{$index}=>quantity",$this->_session->get("cart=>{$index}=>quantity") + 1);
                break;
            case "-":
                if($this->_session->get("cart=>{$index}=>quantity") - 1 <= 0){
                    $this->_session->remove("cart=>{$index}");
                }else{
                    $this->_session->set("cart=>{$index}=>quantity",$this->_session->get("cart=>{$index}=>quantity") - 1);
                }
                break;    
        }
        $data = array(
            "price" => $this->_session->get("cart=>{$index}=>price"),
            "index" => $index,
            "quantity"=> $this->_session->get("cart=>{$index}=>quantity"),
            "totalItems" => $this->countTotalItems(),
            "subtotal" => $this->countSubtotal() 
        );
        if($data["totalItems"] === 0){
            $data['home_url'] = url("/");
        }
        return $data;
    }

    public function deleteItem($index)
    {
        $this->_session->remove("cart=>{$index}");
        $data = array(
            "index" => $index,
            "totalItems" => $this->countTotalItems(),
            "subtotal" => $this->countSubtotal()
        );
        if($data["totalItems"] === 0){
            $data["home_url"] = url("/");
        }
        return $data;
    }

    protected function _checkItemExisted($product_id,$color_id,$size)
    {
        $matchIndex = -1;
        foreach ($this->_session->get("cart") as $index => $cartItem) {
            if($color_id){
                if($size){
                    if (
                    $cartItem['p_id'] == $product_id &&
                    $cartItem['color_id'] == $color_id &&
                    $cartItem['size'] == $size
                    ) {
                        $matchIndex = $index;
                        break;
                    }
                }else{
                    if (
                    $cartItem['p_id'] == $product_id &&
                    $cartItem['color_id'] == $color_id
                    ) {
                        $matchIndex = $index;
                        break;
                    }
                }
            }elseif($size){
                if (
                $cartItem['p_id'] == $product_id &&
                $cartItem['size'] == $size
                ) {
                    $matchIndex = $index;
                    break;
                }
            }else{
                if (
                $cartItem['p_id'] == $product_id
                ) {
                    $matchIndex = $index;
                    break;
                }
            }
        }
        if($matchIndex !== -1)
        {
            return $matchIndex;
        }
        return false;
    }

    public function countTotalItems()
    {
        if($this->_session->has("cart")){
            $totalItems = 0;
            foreach($this->_session->get("cart") as $item){
                $totalItems += $item['quantity'];
            }
            return $totalItems;
        }else{
            return 0;
        }
    }

    public function countSubtotal()
    {
        if($this->_session->has("cart")){
            $subtotal = 0;
            foreach($this->_session->get("cart") as $item){
                $subtotal += $item['quantity'] * $item['price'];
            }
            return $subtotal;
        }else{
            return 0;
        }
    }

    public function reset()
    {
        $this->_session->set("cart",array());
    }

}