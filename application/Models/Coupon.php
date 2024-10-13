<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class Coupon extends Model
{    
    public function insertUsageHistory($user_id,$order_id)
    {
        $this->table("coupon_usage")->insert([
            "user_id" => $user_id,
            "coupon_id" => $this->id,
            "order_id" => $order_id,
        ]);
    }

    public function getUserUsage($user_id)
    {
        return $this->table("coupon_usage")
            ->where("user_id",$user_id)
            ->where("coupon_id",$this->id)
            ->count();
    }

    public function incrementUsage()
    {
        $this->coupon_used = $this->coupon_used + 1;
        $this->save();
    }

    public function apply($user_id,$cart_subtotal)
    {
        $user_usage = $this->getUserUsage($user_id);
        if($user_usage >= $this->per_user){
            return array("status"=>false,"message" => "Người mua chỉ có thể dùng mã \"{$this->code}\" {$this->per_user} lần");
        }
        $current_time = time();
        if($cart_subtotal < $this->minimum_spend){
            return array("status"=>false,"message" => "Giá trị đơn hàng không hợp lệ!");
        }
        if(strtotime($this->start_time) > $current_time || $current_time > strtotime($this->end_time)){
            return array("status"=>false,"message" => "Mã đã hết hạn sử dụng!");
        }
        if($this->coupon_usage <= $this->coupon_used){
            return array("status"=>false,"message" => "Mã đã hết lượt sử dụng!");
        }
        return array("status"=>true,"message"=>"Áp dụng mã giảm giá thành công","amount"=>$this->amount,"coupon_code"=>$this->code);
    }
}       