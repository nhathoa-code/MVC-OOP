<?php

namespace NhatHoa\App\Models;

use NhatHoa\App\Services\CartService;
use NhatHoa\App\Services\InventoryService;
use NhatHoa\Framework\Abstract\Model;
use NhatHoa\App\Services\Interfaces\Payment;

class Order extends Model
{
    public function getList($status, $limit, $currentPage, $keyword)
    {
        if($status){
            $query = $this->where("status","=",$status)->orderBy("orders.created_at","desc");
        }else{
            $query = $this->orderBy("orders.created_at","desc");
        }
        if($keyword){
            $query->where(function($query) use($keyword){
                $query->where("orders.id","like","%{$keyword}%")->orWhere("orders.name","like","%{$keyword}%")->orWhere("users.name","like","%{$keyword}%");
            });
        }
        $total_orders = $query->leftJoin("users","users.id","=","orders.user_id")->select(["orders.*","users.name"])->count(false);
        $orders = $query->limit($limit)->offset(($currentPage - 1) * $limit)->get();
        return [$orders, $total_orders];
    }

    public function getOrder($id)
    {
        $order = $this->first(where:array("id"=>$id));
        if(!$order){
            return null;
        }
        $order->meta = $order->getMetas();
        if(!empty($order->meta)){
            $order->meta = array_map(function($item){
                return ["$item->meta_key" => $item->meta_value];
            },$order->meta);
            $order->meta = array_merge(...$order->meta);
        }
        $order->items = array_map(function($item)
        {
            if($item->p_color_id){
                $item->image = getFiles("images/products/{$item->dir}/{$item->gallery_dir}")[0];
            }else{
                $item->image =  getFiles("images/products/{$item->dir}/product_images")[0];
            }
            return $item;
        },$this->table("order_items as oi")->select(["oi.*","p.p_name","p.dir","pc.color_image","pc.color_name","pc.gallery_dir"])->join("products as p","p.id","=","oi.p_id")->leftJoin("product_colors as pc","pc.id","=","oi.p_color_id")->where("order_id",$order->id)->get());
        return $order;
    }

    public function countOrders($status = null)
    {
        if(!$status){
            return $this->count();
        }else{
            return $this->count(where:array("status"=>$status));
        }
    }

    public function getItems()
    {
        return $this->table("order_items")->where("order_id",$this->id)->get();
    }

    public function getTopSaled($limit)
    {
        return $this->table("order_items ot")
            ->join("products as p","p.id","=","ot.p_id")
            ->join("orders as o","o.id","=","ot.order_id")
            ->leftJoin("product_colors as pcl","pcl.id","=","ot.p_color_id")
            ->select(["p.p_name","p.id","SUM(ot.quantity) as total_saled_items","pcl.color_name","ot.p_size"])
            ->where("o.status","!=","cancelled")
            ->limit($limit)
            ->orderBy("SUM(ot.quantity)","desc")
            ->groupBy("ot.p_id")
            ->get();
    }

    public function updateOrder($status)
    {
        if($this->status === $status){
            return;
        }
        if($status === "completed"){
            if($this->user_id){
                $user = $this->getUser($this->user_id);
                $current_rank = $user->getMeta("rank");
                $point_to_plus = $this->getPointToPlus($current_rank);
                $user->plusPoint($point_to_plus);
                $user->insertPointHistory($point_to_plus, 1, $this->id, $this->getTotalPay());
                $current_spend = $user->updateTotalSpend($this->getTotalPay(),"+");
                $user->updateRank($current_spend);
                if($this->status === "cancelled"){
                    $v_point = $this->getMeta("v_point");
                    if($v_point){
                        $user = $this->getUser($this->user_id);
                        $user->insertPointHistory($v_point,0,$this->id,$this->getTotalPay());
                        $user->minusPoint($v_point);
                    }
                    $inventoryService = new InventoryService;
                    $items = $this->getItems();
                    foreach($items as $item){
                        $inventoryService->updateStockFromOrder($item->p_color_id ?? null,$item->p_size ?? null,$item->p_id,$item->quantity,"-");
                    }
                }
                if($this->payment_method === "cod"){
                    $this->paid_status = "Đã thanh toán";
                }
            }
        }else{
            if($this->user_id){
                $point_history_1 = $this->table("point_history")
                        ->where("order_id",$this->id)
                        ->where("user_id",$this->user_id)
                        ->where("action",1)
                        ->first();
                if($point_history_1){
                    $user = $this->getUser($this->user_id);
                    $user->minusPoint($point_history_1->point);
                    $user->deletePointHistory($this->id, 1);
                    $current_spend = $user->updateTotalSpend($this->getTotalPay(),"-");
                    $user->updateRank($current_spend);
                }
                $items = $this->getItems();
                $inventoryService = new InventoryService;
                if($status === "cancelled"){
                    $point_history_0 = $this->table("point_history")
                    ->where("order_id",$this->id)
                    ->where("user_id",$this->user_id)
                    ->where("action",0)
                    ->first();
                    if($point_history_0){
                        $user = $this->getUser($this->user_id);
                        $user->plusPoint($point_history_0->point);
                        $user->deletePointHistory($this->id, 0);
                    }
                    foreach($items as $item){
                        $inventoryService->updateStockFromOrder($item->p_color_id ?? null,$item->p_size ?? null,$item->p_id,$item->quantity,"+");
                    }
                }else{
                    if($this->status === "cancelled"){
                        foreach($items as $item){
                            $inventoryService->updateStockFromOrder($item->p_color_id ?? null,$item->p_size ?? null,$item->p_id,$item->quantity,"-");
                        }
                        $v_point = $this->getMeta("v_point");
                        if($v_point){
                            $user = $this->getUser($this->user_id);
                            $user->insertPointHistory($v_point,0,$this->id,$this->getTotalPay());
                            $user->minusPoint($v_point);
                        }
                    }
                }
            }
            if($this->payment_method === "cod" && strpos($this->paid_status,"Đã thanh toán") !== false){
                $this->paid_status = "Chưa thanh toán";
            }
        }
        $this->status = $status;
        $this->save();
    }

    public function deleteOrder()
    {
        if($this->status === "cancelled"){
            $this->delete();
        }
    }

    public function getUser($user_id)
    {
        $user = new User($this->table("users")->where("id",$user_id)->first());
        return $user;
    }

    public function getTotalPay()
    {
        $coupon_amount = 0;
        $point = 0;
        $order_meta = $this->getMetas();
        if(!empty($order_meta)){
            $order_meta = array_map(function($item){
                return ["$item->meta_key" => $item->meta_value];
            },$order_meta);
            $order_meta = array_merge(...$order_meta);
            if(array_key_exists("coupon",$order_meta)){
                $coupon = unserialize($order_meta['coupon']);
                $coupon_amount = $coupon['coupon_amount'];
            }
            if(array_key_exists("v_point",$order_meta)){
                $point = $order_meta["v_point"];
            }                   
        }
        $total_pay = $this->total - $coupon_amount - $point * 10000;
        if($total_pay < 0){
            $total_pay = 0;
        }
        return $total_pay;
    }
    
    public function getPointToPlus($user_rank)
    {
        $total_pay = $this->getTotalPay();
        switch($user_rank){
            case "member":
                $plus_point = round($total_pay / 200000);
                break;
            case "silver":
                $plus_point = round($total_pay / 175000);
                break;   
            case "gold":
                $plus_point = round($total_pay / 150000);
                break;
            case "platinum":
                $plus_point = round($total_pay / 125000);
                break;   
            case "diamond":
                $plus_point = round($total_pay / 100000);
                break;     
        }
        return $plus_point;
    }

    public function saveOrder($data,$coupon,$V_point,CartService $cartService,InventoryService $inventoryService)
    {
        $this->id = $data["id"];
        $this->user_id = $data["user_id"];
        $this->total = $data["total"];
        $this->payment_method = $data["payment_method"];
        $this->status = $data["status"];
        $this->shipping_fee = $data["shipping_fee"];
        $this->coupon = $data["coupon"] ?? 0;
        $this->name = $data["name"];
        $this->email = $data["email"];
        $this->phone = $data["phone"];
        $this->address = $data["address"];
        $this->note = $data["note"];
        $this->paid_status = "Chưa thanh toán";
        $this->save();
        if(isset($coupon)){
            $this->insertMeta("coupon",serialize(array("coupon_code"=>$coupon->code,"coupon_amount"=>$coupon->amount)));
            $coupon->incrementUsage();
            $coupon->insertUsageHistory(getUser()->id,$this->id);
        }
        if(isset($V_point)){
            $this->insertMeta("v_point",$V_point);
            $user = $this->getUser($this->user_id);
            $user->insertPointHistory($V_point,0,$this->id,$this->total);
            $user->updateMeta("point",$user->getMeta("point") - $V_point);
        }
        foreach($cartService->getItems() as $item){
            $this->insertItem($item["p_id"],$item["quantity"],$item["price"],$item["size"] ?? null,$item["color_id"] ?? null);
            $inventoryService->updateStockFromOrder($item["color_id"]?? null,$item["size"] ?? null,$item["p_id"],$item["quantity"],"-");
        }
    }

    public function insertItem($p_id,$quantity,$price,$size,$color_id)
    {
        $this->table("order_items")->insert([
            "order_id" => $this->id,
            "p_id" => $p_id,
            "quantity" => $quantity,
            "p_price" => $price,
            "p_size" => $size,
            "p_color_id" => $color_id
        ]);
    }

    public function insertMeta($meta_key,$meta_value)
    {
        $this->table("order_meta")->insert([
            "order_id" => $this->id,
            "meta_key" => $meta_key,
            "meta_value" => $meta_value
        ]);
    }

    public function getMeta($meta_key)
    {
        return $this->table("order_meta")
                    ->where("order_id",$this->id)
                    ->where("meta_key",$meta_key)
                    ->first("meta_value");
    }

    public function getMetas()
    {
        return $this->table("order_meta")
                    ->where("order_id",$this->id)
                    ->get();
    }

    public function getMapStatus()
    {
        return array(
                "pending" => "Chờ xác nhận",
                "toship" => "Chờ lấy hàng",
                "shipping" => "Đang vận chuyển",
                "completed" => "Hoàn thành",
                "cancelled" => "Đã hủy",
            );
    }

    public function processPayment(Payment $payment)
    {
        $payment->process($this->id,$this->getTotalPay());
    }
}

