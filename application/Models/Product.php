<?php

namespace NhatHoa\App\Models;

use NhatHoa\Framework\Abstract\Model;
use NhatHoa\Framework\Facades\DB;

class Product extends Model
{    
    public function getSizeChart()
    {
        $size_chart_id = DB::table("product_size_chart_link")
                            ->where("product_id",$this->id)
                            ->first("size_chart_id");
        return SizeChart::first(where:array("id"=>$size_chart_id));
    }

    public function updateSizeChart($size_chart_id)
    {
        $check = $this->table("product_size_chart_link")
                    ->where("product_id",$this->id)
                    ->exists();
        if($check){
            $this->table("product_size_chart_link")
                ->where("product_id",$this->id)
                ->update([
                    "size_chart_id" => $size_chart_id
                ]);
        }else{
            $this->table("product_size_chart_link")
                ->insert([
                    "product_id" => $this->id,
                    "size_chart_id" => $size_chart_id
                ]);
        }
    }

    public function deleteSizeChart()
    {
        $this->table("product_size_chart_link")
            ->where("product_id",$this->id)
            ->limit(1)
            ->delete();
    }

    public function hasColorsSizes()
    {
        $colors_sizes = $this->exists(
            table:"product_colors_sizes",
            where:array("p_id" => $this->id)
        );
        if($colors_sizes) return true;
        return false;
    }

    public function hasColors()
    {
        $colors = $this->exists(
            table:"product_colors",
            where:array("p_id" => $this->id)
        );
        if($colors) return true;
        return false;
    }

    public function hasSizes()
    {
        $sizes = $this->exists(
            table:"product_sizes",
            where:array("p_id" => $this->id)
        );
        if($sizes) return true;
        return false;
    }

    public function getColorsSizes()
    {
        return $this->table("product_colors_sizes as pcs")
                ->select(["pc.color_name","pcs.size","pcs.price","pcs.stock"])
                ->join("product_colors as pc","pcs.color_id","=","pc.id")
                ->where("pcs.p_id",$this->id)
                ->get();
    }

    public function getColors()
    {
        return $this->table("product_colors")
                ->where("p_id",$this->id)
                ->get();
    }

    public function getSizes()
    {
        return $this->table("product_sizes")
                ->where("p_id",$this->id)
                ->get();
    }

    public function getRelated($limit)
    {
        $categories = $this->categories;
        $last_cat = end($categories);
        $p_ids = $this->table("product_categories")
            ->where("cat_id",$last_cat)
            ->limit($limit)
            ->select(["p_id"])
            ->getArray("p_id");
        return $this->all(
            where:array(array("id"=>$this->id,"operator"=>"!=")),
            whereIn:array("id"=>$p_ids),
            orderBy:array("created_at"=>"desc")
        );
    }
}
