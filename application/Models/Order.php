<?php

namespace NhatHoa\App\Models;

use NhatHoa\Framework\Abstract\Model;
use NhatHoa\App\Services\Interfaces\Payment;

class Order extends Model
{
    public function getItems()
    {
        return $this->table("order_items")
                    ->where("order_id",$this->id)
                    ->get();
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

    public function processPayment(Payment $payment)
    {
        $payment->process($this->id,$this->getTotalPay());
    }
}

