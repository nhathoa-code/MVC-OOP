<?php

namespace NhatHoa\App\Services;
use NhatHoa\Framework\Abstract\Service;
use NhatHoa\Framework\Facades\DB;
use NhatHoa\Framework\Facades\PDF;

class InvoiceService extends Service
{
    public static function generate($sale_id)
    {
        $sale = DB::table("sales as s")->where("s.id",$sale_id)
            ->select(["s.id","s.created_at","t.name","t.address","c.name as customer_name","c.phone as customer_phone","p.name as province","pd.name as district","u.name as employee"])
            ->join("sale_items as si","si.sale_id","=","s.id")
            ->join("customers as c","c.id","=","s.customer_id")
            ->join("users as u","u.id","=","s.employee_id")
            ->join("stores as t","t.id","=","s.store_id")
            ->join("provinces as p","p.id","=","t.province_id")
            ->join("province_districts as pd","pd.id","=","t.district_id")
            ->first();
        if(!$sale){
            return "Không tìm thấy hóa đơn";
        }
        $sale->items = DB::table("sale_items as si")
            ->where("sale_id",$sale->id)
            ->select(["si.*","p.p_name as product_name","pc.color_name"])
            ->join("products as p","si.product_id","=","p.id")
            ->leftJoin("product_colors as pc","si.color_id","=","pc.id")
            ->get();
        PDF::loadTemplate("admin/pos/invoice/template")->setData(["sale"=>$sale])->generate();
    }
}