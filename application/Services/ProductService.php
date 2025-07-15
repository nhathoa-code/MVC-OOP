<?php

namespace NhatHoa\App\Services;
use NhatHoa\Framework\Abstract\Service;
use NhatHoa\App\Models\Product;
use NhatHoa\Framework\Facades\DB;

class ProductService extends Service
{
    public static function mapProducts($products)
    {
        return array_map(function($item){
           $item->product_images = getFiles("images/products/{$item->dir}/product_images");
           if($item->hasColorsSizes()){
                $item->colors_sizes = DB::table("product_colors_sizes as pcs")->select(["pc.color_name","pcs.size","pcs.price","pcs.stock"])->join("product_colors as pc","pcs.color_id","=","pc.id")->where("pcs.p_id",$item->id)->get();
           }elseif($item->hasColors()){
                $item->colors = $item->getColors();
           }elseif($item->hasSizes()){
                $item->sizes = $item->getSizes();
           }
           return $item;
        },$products);
    }

    public static function generateNumericID($length = 10) 
    {
        $id = 'VNH';
        for ($i = 0; $i < $length; $i++) {
            $id .= mt_rand(0, 9); 
        }
        return $id;
    }

    public static function filter($request,$category_id,$limit,$page)
    {
        $query = DB::table("product_categories as pc")
                    ->join("products as p","p.id","=","pc.p_id")
                    ->leftJoin("product_colors as pcl","pcl.p_id","=","pc.p_id")
                    ->leftJoin("product_sizes as ps","ps.p_id","=","pc.p_id")
                    ->leftJoin("product_colors_sizes as pcs","pcs.p_id","=","pc.p_id")
                    ->leftJoin("product_colors as pc1","pc1.id","=","pcs.color_id")
                    ->select(['p.*'])
                    ->where("pc.cat_id",$category_id);
        if($request->hasQuery("color")){
            $colors = (array) $request->query("color");
            $query->where(function($query) use($colors){
                foreach($colors as $index => $color){
                    if($index === 0){
                        $query->where("pcl.color_name","like","%{$color}%");
                    }else{
                        $query->orWhere("pcl.color_name","like","%{$color}%");
                    }
                }
            });
        }
        if($request->hasQuery("size")){
            $sizes = (array) $request->query("size");
            $query->where(function($query) use ($sizes){
                foreach($sizes as $index => $size){
                    if(count($sizes) === 1){
                        $query->where(function($query) use($size){
                            $query->where("ps.size","like","%{$size}%")
                                ->where("ps.stock",">",0);
                        });
                        $query->orWhere(function($query) use($size){
                             $query->where("pcs.size","like","%{$size}%")
                                ->where("pcs.stock",">",0);
                        });
                    }else{
                        $query->orWhere(function($query) use ($size){
                            $query->where(function($query) use($size){
                                $query->where("ps.size","like","%{$size}%")
                                    ->where("ps.stock",">",0);
                            });
                            $query->orWhere(function($query) use($size){
                                $query->where("pcs.size","like","%{$size}%")
                                    ->where("pcs.stock",">",0);
                            });
                        });        
                    }        
                }
            });
        }
        if($request->hasQuery("price")){
            $prices = (array) $request->query("price");
            $query->where(function($query) use ($prices){
                foreach($prices as $index => $price){
                    $query->orWhere(function($query) use($price){
                        $min = explode("-",$price)[0];
                        $max = explode("-",$price)[1];
                        $query->where(function($query) use ($min,$max){
                                $query->where("p.p_price",">=",$min)
                                    ->Where("p.p_price","<=",$max)
                                    ->where("p.p_price",">",0);
                        });
                        $query->orWhere(function($query) use($min,$max){
                            $query->where("pcl.price",">=",$min)
                                    ->Where("pcl.price","<=",$max)
                                    ->where("pcl.price",">",0);
                        });         
                        $query->orWhere(function($query) use($min,$max){
                            $query->where("ps.price",">=",$min)
                                    ->Where("ps.price","<=",$max)
                                    ->where("ps.price",">",0);
                        }); 
                        $query->orWhere(function($query) use($min,$max){
                            $query->where("pcs.price",">=",$min)
                                    ->Where("pcs.price","<=",$max)
                                    ->Where("pcs.price",">",0);
                        });                  
                    });    
                }
            });
        }
        $number_of_products = $query->count(false,context:"DISTINCT p.id");
        $total_pages = ceil($number_of_products / $limit);
        $query->distinct();
        if($request->isAjax()){
            $query->limit($limit)->offset(($page - 1) * $limit);
        }else{
            $query->limit($limit * $page);
        }
        $collection = $query->orderBy("p.created_at","desc")->get();
        $collection = array_map((function($item){
            return new Product($item);
        }),$collection);
        return array($collection,$number_of_products,$total_pages);
    }

    public static function search($request,$keyword,$page,$limit)
    {
        $number_of_products = Product::count(
            where:array(
                array(
                    "p_name"=>"%{$keyword}%",
                    "operator"=>"like")
                )
        );
        $total_pages = ceil($number_of_products / $limit);
        $query = Product::where("p_name","like","%{$keyword}%");
        if($request->isAjax()){
            $query->limit($limit)->offset(($page - 1) * $limit);
        }else{
            $query->limit($limit * $page);
        }
        $collection = $query->get();
        return array($collection,$number_of_products,$total_pages);
    }

    public static function getProductsFromCategoryIds(array $ids, int $limit = 20)
    {
        $product_ids = DB::table('product_categories')
                    ->select(['p_id'])
                    ->whereIn('cat_id', $ids)
                    ->limit($limit)
                    ->distinct()
                    ->getArray('p_id');
        return Product::whereIn('id',$product_ids)->get();   
    }

    public static function getFilteredSizes(int $category_id)
    {
        return DB::table("product_categories as pc")
                    ->select(["pcs.size"])
                    ->leftJoin("products as p","p.id","=","pc.p_id")
                    ->leftJoin("product_sizes as ps","ps.p_id","=","p.id")
                    ->leftJoin("product_colors_sizes as pcs","pcs.p_id","=","p.id")
                    ->where("cat_id",$category_id)
                    ->whereNotNull("pcs.size")
                    ->distinct()
                    ->get();
    }

    public static function getFilteredColors(int $category_id)
    {
        return DB::table("product_categories as pc")
                    ->select(["pcl.color_name","pcl.color_image"])
                    ->leftJoin("products as p","p.id","=","pc.p_id")
                    ->leftJoin("product_colors as pcl","pcl.p_id","=","p.id")
                    ->where("cat_id",$category_id)
                    ->whereNotNull("pcl.color_name")
                    ->groupBy("pcl.color_name")
                    ->get();
    }
    
}