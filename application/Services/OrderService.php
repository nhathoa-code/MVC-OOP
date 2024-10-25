<?php

namespace NhatHoa\App\Services;
use NhatHoa\Framework\Abstract\Service;
use NhatHoa\App\Models\Order;
use NhatHoa\Framework\Facades\DB;

class OrderService extends Service
{
    public static function countOrders($status = null)
    {
        if(!$status){
            return Order::count();
        }else{
            return Order::count(where:array("status"=>$status));
        }
    }

    public static function getTopSaled($limit)
    {
        return DB::table("order_items ot")
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

    public static function getMapStatus()
    {
        return array(
                "pending" => "Chờ xác nhận",
                "toship" => "Chờ lấy hàng",
                "shipping" => "Đang vận chuyển",
                "completed" => "Hoàn thành",
                "cancelled" => "Đã hủy",
            );
    }
}