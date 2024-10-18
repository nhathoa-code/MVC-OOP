<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\App\Models\Order;
use NhatHoa\App\Repositories\Interfaces\OrderRepositoryInterface;
use NhatHoa\App\Repositories\BaseRepository;
use NhatHoa\App\Services\CartService;
use NhatHoa\App\Services\InventoryService;
use NhatHoa\App\Services\UserService;
use NhatHoa\Framework\Facades\DB;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function getAll($status,$limit,$currentPage,$keyword) : array
    {
        if($status){
            $query = Order::where("status","=",$status)->orderBy("orders.created_at","desc");
        }else{
            $query = Order::orderBy("orders.created_at","desc");
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

    public function getById($id) : Order|null
    {
        $order = Order::first(where:array("id"=>$id));
        if(!$order) return null;
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
        },DB::table("order_items as oi")->select(["oi.*","p.p_name","p.dir","pc.color_image","pc.color_name","pc.gallery_dir"])->join("products as p","p.id","=","oi.p_id")->leftJoin("product_colors as pc","pc.id","=","oi.p_color_id")->where("order_id",$order->id)->get());
        return $order;
    }


    public function create($data,$coupon,$V_point,CartService $cartService,InventoryService $inventoryService) : Order
    {
        $order = new Order();
        $order->id = $data["id"];
        $order->user_id = $data["user_id"];
        $order->total = $data["total"];
        $order->payment_method = $data["payment_method"];
        $order->status = $data["status"];
        $order->shipping_fee = $data["shipping_fee"];
        $order->coupon = $data["coupon"] ?? 0;
        $order->name = $data["name"];
        $order->email = $data["email"];
        $order->phone = $data["phone"];
        $order->address = $data["address"];
        $order->note = $data["note"];
        $order->paid_status = "Chưa thanh toán";
        $order->save();
        if(isset($coupon)){
            $order->insertMeta("coupon",serialize(array("coupon_code"=>$coupon->code,"coupon_amount"=>$coupon->amount)));
            $coupon->incrementUsage();
            $coupon->insertUsageHistory(getUser()->id,$order->id);
        }
        if(isset($V_point)){
            $order->insertMeta("v_point",$V_point);
            $user = $order->getUser($order->user_id);
            $user->insertPointHistory($V_point,0,$order->id,$order->total);
            $user->updateMeta("point",$user->getMeta("point") - $V_point);
        }
        foreach($cartService->getItems() as $item){
            $order->insertItem($item["p_id"],$item["quantity"],$item["price"],$item["size"] ?? null,$item["color_id"] ?? null);
            $inventoryService->updateStockFromOrder($item["color_id"]?? null,$item["size"] ?? null,$item["p_id"],$item["quantity"],"-");
        }
        return $order;
    }

    public function update(Order $order,$status,UserService $userService) : void
    {
        if($order->status === $status) return;
        if($status === "completed"){
            if($order->user_id){
                $user = $order->getUser($order->user_id);
                $current_rank = $user->getMeta("rank");
                $point_to_plus = $order->getPointToPlus($current_rank);
                $userService->plusPoint($user,$point_to_plus);
                $userService->insertPointHistory($user,$point_to_plus, 1, $order->id, $order->getTotalPay());
                $current_spend = $userService->updateTotalSpend($user,$order->getTotalPay(),"+");
                $userService->updateRank($user,$current_spend);
                if($order->status === "cancelled"){
                    $v_point = $order->getMeta("v_point");
                    if($v_point){
                        $user = $order->getUser($order->user_id);
                        $userService->insertPointHistory($user,$v_point,0,$order->id,$order->getTotalPay());
                        $userService->minusPoint($user,$v_point);
                    }
                    $inventoryService = new InventoryService;
                    $items = $order->getItems();
                    foreach($items as $item){
                        $inventoryService->updateStockFromOrder($item->p_color_id ?? null,$item->p_size ?? null,$item->p_id,$item->quantity,"-");
                    }
                }
                if($order->payment_method === "cod"){
                    $order->paid_status = "Đã thanh toán";
                }
            }
        }else{
            if($order->user_id){
                $point_history_1 = DB::table("point_history")
                        ->where("order_id",$order->id)
                        ->where("user_id",$order->user_id)
                        ->where("action",1)
                        ->first();
                if($point_history_1){
                    $user = $order->getUser($order->user_id);
                    $userService->minusPoint($user,$point_history_1->point);
                    $userService->deletePointHistory($user,$order->id, 1);
                    $current_spend = $userService->updateTotalSpend($user,$order->getTotalPay(),"-");
                    $userService->updateRank($user,$current_spend);
                }
                $items = $order->getItems();
                $inventoryService = new InventoryService;
                if($status === "cancelled"){
                    $point_history_0 = DB::table("point_history")
                    ->where("order_id",$order->id)
                    ->where("user_id",$order->user_id)
                    ->where("action",0)
                    ->first();
                    if($point_history_0){
                        $user = $order->getUser($order->user_id);
                        $userService->plusPoint($user,$point_history_0->point);
                        $userService->deletePointHistory($user,$order->id, 0);
                    }
                    foreach($items as $item){
                        $inventoryService->updateStockFromOrder($item->p_color_id ?? null,$item->p_size ?? null,$item->p_id,$item->quantity,"+");
                    }
                }else{
                    if($order->status === "cancelled"){
                        foreach($items as $item){
                            $inventoryService->updateStockFromOrder($item->p_color_id ?? null,$item->p_size ?? null,$item->p_id,$item->quantity,"-");
                        }
                        $v_point = $order->getMeta("v_point");
                        if($v_point){
                            $user = $order->getUser($order->user_id);
                            $userService->insertPointHistory($user,$v_point,0,$order->id,$order->getTotalPay());
                            $userService->minusPoint($user,$v_point);
                        }
                    }
                }
            }
            if($order->payment_method === "cod" && strpos($order->paid_status,"Đã thanh toán") !== false){
                $order->paid_status = "Chưa thanh toán";
            }
        }
        $order->status = $status;
        $order->save();
    }

    public function delete(Order $order) : void
    {
        if($order->status === "cancelled"){
            $order->delete();
        }
    }
}