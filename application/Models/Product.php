<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;
use Ramsey\Uuid\Uuid;

class Product extends Model
{
    public function getList($currentPage, $limit, $keyword)
    {
        if($keyword){
            $query = $this->where("p_name","like","%{$keyword}%")->orWhere("id","like","%{$keyword}%");
        }else{
            $query = $this->orderBy("created_at","desc");
        }
        $number_of_products = $query->count(false);
        $products = $query->limit($limit)->offset(($currentPage - 1) * $limit)->get();
        $products = $this->mapProducts($products);
        return array($products,$number_of_products);
    }

    public function mapProducts($products)
    {
        return array_map(function($item){
           $item->product_images = getFiles("images/products/{$item->dir}/product_images");
           if($item->hasColorsSizes()){
                $item->colors_sizes = $this->table("product_colors_sizes as pcs")->select(["pc.color_name","pcs.size","pcs.price","pcs.stock"])->join("product_colors as pc","pcs.color_id","=","pc.id")->where("pcs.p_id",$item->id)->get();
           }elseif($item->hasColors()){
                $item->colors = $item->getColors();
           }elseif($item->hasSizes()){
                $item->sizes = $item->getSizes();
           }
           return $item;
        },$products);
    }

    public function getLatest($number_of_products)
    {
        return $this->all(orderBy:array("id"=>"desc"),limit:$number_of_products);
    }

    public function countProductsFromCategory($category_id)
    {
        return $this->table("product_categories")
                    ->where("cat_id",$category_id)
                    ->count();
    }

    public function saveProduct($validated,$request)
    {
        $dir = Uuid::uuid4();
        $this->id = $this->generateNumericID();
        $this->p_name = $validated['p_name'];
        $this->p_price = $validated['p_price'] ?? 0;
        $this->p_stock = $validated['p_stock'] ?? 0;
        $this->p_desc = $validated['p_desc'];
        $this->dir = $dir;
        $this->save();
        foreach($request->input("categories") as $cat){
            $this->table("product_categories")->insert([
                "p_id" => $this->id,
                "cat_id" => $cat
            ]);
        }
        if($request->hasFile("p_images")){
            foreach($request->file("p_images") as $key => $file){
                $file_name = $file->name();
                $file_full_name = ($key + 1) . "_" . $file_name;
                $file->save("images/products/{$dir}/product_images",$file_full_name);
            }
        }       
        if($request->hasFile("colors")){
            foreach($request->file("colors") as $index => $file){
                $color_image = $file->save("images/products/{$dir}/colors");
                $gallery_dir = Uuid::uuid4();
                $color_id = $this->table("product_colors")->insert([
                    "p_id" => $this->id,
                    "color_image" => $color_image,
                    "color_name" => $validated["name_of_color_{$index}"],
                    "price" => $validated["price_of_color_{$index}"] ?? 0,
                    "stock" => $validated["stock_of_color_{$index}"] ?? 0,
                    "gallery_dir" => $gallery_dir
                ]);
                if($request->hasFile("gallery_of_color_{$index}")){
                    foreach($request->file("gallery_of_color_{$index}") as $key => $file){
                        $file_name = $file->name();
                        $file_full_name = ($key + 1) . "_" . $file_name;
                        $file->save("images/products/{$dir}/{$gallery_dir}",$file_full_name);
                    }
                }
                if($request->has("sizes_of_color_{$index}")){
                    foreach($request->input("sizes_of_color_{$index}") as $item){
                        $this->table("product_colors_sizes")->insert([
                            "p_id" => $this->id,
                            "color_id" => $color_id,
                            "size" => $item['size'] ?? "",
                            "price" => $item['price'] ?? 0,
                            "stock" => $item['stock'] ?? 0
                        ]);
                    }
                }
            }
        }else if($request->has("sizes")){
            foreach($request->input("sizes") as $size){
                $this->table("product_sizes")->insert([
                    "p_id" => $this->id,
                    "size" => $size['value'] ?? "",
                    "price" => $size['price'] ?? 0,
                    "stock" => $size['stock'] ?? 0,
                ]);
            }
        }
        if(!empty($validated["size_chart"])){
            $this->updateSizeChart($validated["size_chart"]);
        }
    }
    
    public function getProduct($id)
    {
        $product = $this->first(where:array("id" => $id));
        if(!$product){
            return false;
        }
        $product->categories = array_map(function($item){
            return $item->cat_id;
        },$this->table("product_categories")->where("p_id",$product->id)->get());
        $product->images = getFiles("images/products/{$product->dir}/product_images");
        $product->size_chart = $this->getSizeChartModel()->getSizeChartForProduct($product->id);
        if($product->hasColorsSizes()){
            $colors = $product->getColors();
            $product->colors_sizes = array_map(function($color) use($product){
                $sizes = $this->table("product_colors_sizes")->select(["size","price","stock"])->where("p_id",$product->id)->where("color_id",$color->id)->get();
                if(!empty($sizes)){
                    $color->sizes = $sizes;
                }
                $color->gallery_images = getFiles("images/products/{$product->dir}/{$color->gallery_dir}");
                return $color;
            },$colors);
        }else if($product->hasColors()){
            $colors = $product->getColors();
            $product->colors = array_map(function($color) use($product){
                $color->gallery_images = getFiles("images/products/{$product->dir}/{$color->gallery_dir}");
                return $color;
            },$colors);
        }else if($product->hasSizes()){
            $sizes = $product->getSizes();
            $product->sizes = $sizes;
        }
        return $product;
    }

    public function updateProduct($validated,$request,$inventoryService)
    {
        $this->p_name = $validated['p_name'];
        $this->p_price = $validated['p_price'] ?? 0;
        $this->p_stock = $validated['p_stock'] ?? 0;
        $this->p_desc = $validated['p_desc'];

        $categories_from_db = array_map(function($item){
            return $item->cat_id;
        }, $this->table("product_categories")->where("p_id",$this->id)->get());

        $categories_from_client = $request->input("categories");

        $full_categories_diff = array_merge(array_diff($categories_from_db,$categories_from_client),array_diff($categories_from_client,$categories_from_db));

        foreach($full_categories_diff as $item){
            if(in_array($item,$categories_from_db)){
                $this->table("product_categories")->where("p_id", $this->id)->where("cat_id",$item)->delete();
            }else{
                $this->table("product_categories")->insert([
                    "p_id" => $this->id,
                    "cat_id" => $item
                ]);
            }
        }
       
        if($request->hasFile("p_images")){
            $p_images_meta = $request->input("p_images_meta");
            if($request->has("p_images_to_delete")){
                foreach($request->input("p_images_to_delete") as $image){
                    delete_file("/images/products/{$this->dir}/product_images/{$image}");
                }
            }
            foreach($request->file("p_images") as $index => $file){
                if($p_images_meta[$index]["status"] === "new"){
                    $file_name = $file->name();
                    $file_full_name = ($index + 1) . "_" . $file_name;
                    $file->save("images/products/{$this->dir}/product_images",$file_full_name);
                }else{
                    $parts = explode("_",$file->name(),2);
                    $new_name = ($index + 1) . "_" . $parts[1];
                    rename_file("images/products/{$this->dir}/product_images/{$file->name()}","images/products/{$this->dir}/product_images/$new_name");
                }
            }
        }
     
        if($request->has("colors_to_delete")){
            foreach($request->input("colors_to_delete") as $color){
                $this->table("product_colors")->where("p_id",$this->id)->where("id",$color["color_id"])->delete();
                remove_dir("images/products/{$this->dir}/{$color['gallery_dir']}");
                delete_file("{$color['color_path']}");
            }
        }

        if($request->hasFile("colors")){
            $meta_of_color = $validated["meta_of_color"];
            $colors_from_db = $this->table("product_colors")->select(["id"])->where("p_id",$this->id)->get();
            foreach($request->file("colors") as $index => $file){
                $status = $meta_of_color[$index]['status'];
                $color_id = $status === "old" ? $meta_of_color[$index]['color_id'] : 0;
                $color_name = $validated["name_of_color_{$index}"];
                $gallery_dir = $status === "new" ? Uuid::uuid4() : $meta_of_color[$index]['gallery_dir'];
                if($status === "new"){
                    $color_image = $file->save("images/products/{$this->dir}/colors");
                    if($request->hasFile("gallery_of_color_{$index}")){
                        foreach($request->file("gallery_of_color_{$index}") as $key => $file){
                            $file_name = $file->name();
                            $file_full_name = ($key + 1) . "_" . $file_name;
                            $file->save("images/products/{$this->dir}/{$gallery_dir}",$file_full_name);
                        }
                    }
                }else{
                    if($request->has("new_image_color_{$index}")){
                        delete_file("{$meta_of_color[$index]['path']}");
                        $color_image = $file->save("images/products/{$this->dir}/colors");
                    }else{
                        $color_image = $meta_of_color[$index]['path'];
                    }
                    $meta_gallery_of_color = $validated["meta_gallery_of_color_{$index}"];
                    if($request->hasFile("gallery_of_color_{$index}")){
                        if($request->has("gallery_images_to_delete_of_color_{$index}")){
                            foreach($validated["gallery_images_to_delete_of_color_{$index}"] as $image){
                                delete_file("images/products/{$this->dir}/{$gallery_dir}/{$image}");
                            }
                        }
                        foreach($request->file("gallery_of_color_{$index}") as $key => $file){              
                            if($meta_gallery_of_color[$key]["status"] === "new"){
                                $file_name = $file->name();
                                $file_full_name = ($key + 1) . "_" . $file_name;
                                $file->save("images/products/{$this->dir}/{$gallery_dir}",$file_full_name);
                            }else{
                                $parts = explode("_",$file->name(),2);
                                $new_name = ($key + 1) . "_" . $parts[1];
                                rename_file("images/products/{$this->dir}/{$gallery_dir}/{$file->name()}","images/products/{$this->dir}/{$gallery_dir}/$new_name");
                            }
                        }
                    }
                }
                if(isset($colors_from_db[$index])){
                    $this->table("product_colors")->where("p_id",$this->id)->where("id",$colors_from_db[$index]->id)->update([
                        "color_image" => $color_image,
                        "color_name" => $color_name,
                        "price" => $validated["price_of_color_{$index}"] ?? 0,
                        "stock" => $validated["stock_of_color_{$index}"] ?? 0,
                        "gallery_dir" => $gallery_dir
                    ]);
                }else{
                    $color_id = $this->table("product_colors")->insert([
                        "p_id" => $this->id,
                        "color_image" => $color_image,
                        "color_name" => $validated["name_of_color_{$index}"],
                        "price" => $validated["price_of_color_{$index}"] ?? 0,
                        "stock" => $validated["stock_of_color_{$index}"] ?? 0,
                        "gallery_dir" => $gallery_dir
                    ]);
                }

                if($request->has("sizes_of_color_{$index}")){
                    $inventoryService->updateProductInventoryInStores(
                        $this,
                        [
                            "type" => "color",
                            "action" => "delete",
                            "color" => $color_id,
                        ]
                    );
                    $colors_sizes_from_db = $this->table("product_colors_sizes")->where("p_id",$this->id)->where("color_id",$color_id)->get();
                    foreach($request->input("sizes_of_color_{$index}") as $size_index => $item){
                        if(isset($colors_sizes_from_db[$size_index])){
                            $this->table("product_colors_sizes")->where("id",$colors_sizes_from_db[$size_index]->id)->update([
                                "size" => $item['size'],
                                "price" => $item['price'],
                                "stock" => $item['stock']
                            ]);
                        }else{
                            $this->table("product_colors_sizes")->insert([
                                "p_id" => $this->id,
                                "color_id" => $color_id,
                                "size" => $item['size'],
                                "price" => $item['price'],
                                "stock" => $item['stock']
                            ]);
                            $inventoryService->updateProductInventoryInStores(
                                $this,
                                [
                                    "type" => "color_size",
                                    "action" => "insert",
                                    "color" => $color_id,
                                    "size" => $item["size"],
                                    "stock" => $item["stock"],
                                    "price" => $item["price"]
                                ]
                            );
                        }
                    }
                    if(count($colors_sizes_from_db) > count($request->input("sizes_of_color_{$index}"))){
                        for($i = count($validated["sizes_of_color_{$index}"]); $i < count($colors_sizes_from_db); $i++)
                        {
                            $this->table("product_colors_sizes")->where("id",$colors_sizes_from_db[$i]->id)->delete();
                            $inventoryService->updateProductInventoryInStores(
                                $this,
                                [
                                    "type" => "color_size",
                                    "action" => "delete",
                                    "color" => $color_id,
                                    "size" => $colors_sizes_from_db[$i]->size
                                ]
                            );
                        }   
                    }
                }else{
                    $this->table("product_colors_sizes")->where("p_id",$this->id)->where("color_id",$color_id)->delete();
                    $inventoryService->updateProductInventoryInStores(
                        $this,
                        [
                            "type" => "color_size",
                            "action" => "delete-all-size",
                            "color" => $color_id,
                        ]
                    );
                }
            }
            if($this->hasSizes()){
                $this->table("product_sizes")->where("p_id",$this->id)->delete();
                $inventoryService->updateProductInventoryInStores(
                    $this,
                    [
                        "type" => "size",
                        "action" => "delete",
                        "all" => true
                    ]
                );
            }
        }else{
            if($this->hasColorsSizes() || $this->hasColors()){
                $colors = $this->table("product_colors")->where("p_id",$this->id)->get();
                foreach($colors as $color){
                    remove_dir("images/products/{$this->dir}/{$color->gallery_dir}");
                    delete_file("{$color->color_image}");
                    $this->table("product_colors")->where("id",$color->id)->delete();
                }
            }
            if($request->has("sizes")){
                $sizes_from_db = $this->table("product_sizes")->where("p_id",$this->id)->get();
                foreach($request->input("sizes") as $index => $size){
                    if(isset($sizes_from_db[$index])){
                        $this->table("product_sizes")->where("id",$sizes_from_db[$index]->id)->where("p_id",$this->id)->update([
                            "size" => $size['value'],
                            "price" => $size['price'],
                            "stock" => $size['stock']
                        ]);
                    }else{
                        $this->table("product_sizes")->insert([
                            "p_id" => $this->id,
                            "size" => $size['value'],
                            "price" => $size['price'],
                            "stock" => $size['stock']
                        ]);
                        $inventoryService->updateProductInventoryInStores(
                            $this,
                            [
                                "type" => "size",
                                "action" => "insert",
                                "size" => $size['value'],
                                "stock" => $size["stock"],
                                "price" => $size["price"]
                            ]
                        );
                    }
                }
                if(count($sizes_from_db) > count($request->input("sizes"))){
                    for($i = count($request->input("sizes")); $i < count($sizes_from_db); $i++){
                        $this->table("product_sizes")->where("id",$sizes_from_db[$i]->id)->delete();
                        $inventoryService->updateProductInventoryInStores(
                            $this,
                            [
                                "type" => "size",
                                "action" => "delete",
                                "size" => $sizes_from_db[$i]->size
                            ]
                        );
                    }   
                }
            }else{
                $this->table("product_sizes")->where("p_id",$this->id)->delete();
                $inventoryService->updateProductInventoryInStores(
                    $this,
                    [
                        "type" => "size",
                        "action" => "delete",
                        "all" => true
                    ]
                );
            }
        }
        if(!$this->hasColorsSizes() && !$this->hasColors() && !$this->hasSizes()){
            $inventoryService->updateProductInventoryInStores(
                    $this,
                    [
                        "variant" => false
                    ]
                );
        }
        if(!empty($validated["size_chart"])){
            $this->updateSizeChart($validated["size_chart"]);
        }else{
            $this->deleteSizeChart();
        }
        $this->save();
    }

    public function deleteProduct()
    {
        remove_dir("images/products/{$this->dir}");
        $this->delete();
    }

    public function getSizeChartModel()
    {
        return new SizeChart();
    }

    public function updateSizeChart($size_chart_id)
    {
        $count = $this->table("product_size_chart_link")
                    ->where("product_id",$this->id)
                    ->count();
        if($count == 0){
            $this->table("product_size_chart_link")->insert([
                "product_id" => $this->id,
                "size_chart_id" => $size_chart_id
            ]);
        }
        if($count > 0){
            $this->table("product_size_chart_link")
            ->where("product_id",$this->id)
            ->update([
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
        $colors_sizes = $this->count(table:"product_colors_sizes",where:array("p_id" => $this->id));
        if($colors_sizes > 0){
            return true;
        }else{
            return false;
        }
    }

    public function hasColors()
    {
        $colors = $this->count(table:"product_colors",where:array("p_id" => $this->id));
        if($colors > 0){
            return true;
        }else{
            return false;
        }
    }

    public function hasSizes()
    {
        $sizes = $this->count(table:"product_sizes",where:array("p_id" => $this->id));
        if($sizes > 0){
            return true;
        }else{
            return false;
        }
    }

    public function getColorsSizes()
    {
        return $this->table("product_colors_sizes as pcs")->select(["pc.color_name","pcs.size","pcs.price","pcs.stock"])->join("product_colors as pc","pcs.color_id","=","pc.id")->where("pcs.p_id",$this->id)->get();
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

    public function generateNumericID($length = 10) 
    {
        $id = 'VNH';
        for ($i = 0; $i < $length; $i++) {
            $id .= mt_rand(0, 9); 
        }
        return $id;
    }

    public function filter($request,$category_id,$limit,$page)
    {
        $query = $this->table("product_categories as pc")
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
            return new self($item);
        }),$collection);
        return array($collection,$number_of_products,$total_pages);
    }

    public function search($request,$keyword,$page,$limit)
    {
        $number_of_products = $this->count(
            where:array(
                array(
                    "p_name"=>"%{$keyword}%",
                    "operator"=>"like")
                )
        );
        $total_pages = ceil($number_of_products / $limit);
        $query = $this->where("p_name","like","%{$keyword}%");
        if($request->isAjax()){
            $query->limit($limit)->offset(($page - 1) * $limit);
        }else{
            $query->limit($limit * $page);
        }
        $collection = $query->get();
        return array($collection,$number_of_products,$total_pages);
    }
}
