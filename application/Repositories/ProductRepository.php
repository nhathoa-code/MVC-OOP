<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\App\Models\Product;
use NhatHoa\Framework\Core\Request;
use NhatHoa\App\Repositories\Interfaces\ProductRepositoryInterface;
use NhatHoa\App\Repositories\BaseRepository;
use NhatHoa\App\Services\InventoryService;
use NhatHoa\App\Services\ProductService;
use NhatHoa\Framework\Facades\DB;
use Ramsey\Uuid\Uuid;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function getAll($currentPage, $limit, $keyword) : array
    {
        if($keyword){
            $query = Product::where("p_name","like","%{$keyword}%")
                        ->orWhere("id","like","%{$keyword}%");
        }else{
            $query = Product::orderBy("created_at","desc");
        }
        $number_of_products = $query->count(false);
        $products = $query->limit($limit)->offset(($currentPage - 1) * $limit)->get();
        $products = ProductService::mapProducts($products);
        return array($products,$number_of_products);
    }

    public function getById($id) : Product|null
    {
        $product = Product::first(where:array("id" => $id));
        if(!$product) return null;
        $product->categories = array_map(function($item){
            return $item->cat_id;
        },DB::table("product_categories")->where("p_id",$product->id)->get());
        $product->images = getFiles("images/products/{$product->dir}/product_images");
        $product->size_chart = $product->getSizeChart();
        if($product->hasColorsSizes()){
            $colors = $product->getColors();
            $product->colors_sizes = array_map(function($color) use($product){
                $sizes = DB::table("product_colors_sizes")->select(["size","price","stock"])->where("p_id",$product->id)->where("color_id",$color->id)->get();
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

    public function getLatest($number_of_products) : array
    {
        return  Product::all(orderBy:array("created_at"=>"desc"),limit:$number_of_products);
    }

    public function create($validated,Request $request) : void
    {
        $dir = Uuid::uuid4();
        $product = new Product();
        $product->id = ProductService::generateNumericID();
        $product->p_name = $validated['p_name'];
        $product->p_price = $validated['p_price'] ?? 0;
        $product->p_stock = $validated['p_stock'] ?? 0;
        $product->p_desc = $validated['p_desc'];
        $product->dir = $dir;
        $product->save();
        foreach($request->input("categories") as $cat){
            DB::table("product_categories")->insert([
                "p_id" => $product->id,
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
                $color_id = DB::table("product_colors")->insert([
                    "p_id" => $product->id,
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
                        DB::table("product_colors_sizes")->insert([
                            "p_id" => $product->id,
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
                DB::table("product_sizes")->insert([
                    "p_id" => $product->id,
                    "size" => $size['value'] ?? "",
                    "price" => $size['price'] ?? 0,
                    "stock" => $size['stock'] ?? 0,
                ]);
            }
        }
        if(!empty($validated["size_chart"])){
            $product->updateSizeChart($validated["size_chart"]);
        }
    }

    public function update(Product $product,$validated,$request, InventoryService $inventoryService) : void
    {
        $product->p_name = $validated['p_name'];
        $product->p_price = $validated['p_price'] ?? 0;
        $product->p_stock = $validated['p_stock'] ?? 0;
        $product->p_desc = $validated['p_desc'];

        $categories_from_db = array_map(function($item){
            return $item->cat_id;
        }, DB::table("product_categories")->where("p_id",$product->id)->get());

        $categories_from_client = $request->input("categories");

        $full_categories_diff = array_merge(array_diff($categories_from_db,$categories_from_client),array_diff($categories_from_client,$categories_from_db));

        foreach($full_categories_diff as $item){
            if(in_array($item,$categories_from_db)){
                DB::table("product_categories")->where("p_id", $product->id)->where("cat_id",$item)->delete();
            }else{
                DB::table("product_categories")->insert([
                    "p_id" => $product->id,
                    "cat_id" => $item
                ]);
            }
        }
       
        if($request->hasFile("p_images")){
            $p_images_meta = $request->input("p_images_meta");
            if($request->has("p_images_to_delete")){
                foreach($request->input("p_images_to_delete") as $image){
                    delete_file("/images/products/{$product->dir}/product_images/{$image}");
                }
            }
            foreach($request->file("p_images") as $index => $file){
                if($p_images_meta[$index]["status"] === "new"){
                    $file_name = $file->name();
                    $file_full_name = ($index + 1) . "_" . $file_name;
                    $file->save("images/products/{$product->dir}/product_images",$file_full_name);
                }else{
                    $parts = explode("_",$file->name(),2);
                    $new_name = ($index + 1) . "_" . $parts[1];
                    rename_file("images/products/{$product->dir}/product_images/{$file->name()}","images/products/{$product->dir}/product_images/$new_name");
                }
            }
        }
     
        if($request->has("colors_to_delete")){
            foreach($request->input("colors_to_delete") as $color){
                DB::table("product_colors")->where("p_id",$product->id)->where("id",$color["color_id"])->delete();
                remove_dir("images/products/{$product->dir}/{$color['gallery_dir']}");
                delete_file("{$color['color_path']}");
            }
        }

        if($request->hasFile("colors")){
            $meta_of_color = $validated["meta_of_color"];
            $colors_from_db = DB::table("product_colors")->select(["id"])->where("p_id",$product->id)->get();
            foreach($request->file("colors") as $index => $file){
                $status = $meta_of_color[$index]['status'];
                $color_id = $status === "old" ? $meta_of_color[$index]['color_id'] : 0;
                $color_name = $validated["name_of_color_{$index}"];
                $gallery_dir = $status === "new" ? Uuid::uuid4() : $meta_of_color[$index]['gallery_dir'];
                if($status === "new"){
                    $color_image = $file->save("images/products/{$product->dir}/colors");
                    if($request->hasFile("gallery_of_color_{$index}")){
                        foreach($request->file("gallery_of_color_{$index}") as $key => $file){
                            $file_name = $file->name();
                            $file_full_name = ($key + 1) . "_" . $file_name;
                            $file->save("images/products/{$product->dir}/{$gallery_dir}",$file_full_name);
                        }
                    }
                }else{
                    if($request->has("new_image_color_{$index}")){
                        delete_file("{$meta_of_color[$index]['path']}");
                        $color_image = $file->save("images/products/{$product->dir}/colors");
                    }else{
                        $color_image = $meta_of_color[$index]['path'];
                    }
                    $meta_gallery_of_color = $validated["meta_gallery_of_color_{$index}"];
                    if($request->hasFile("gallery_of_color_{$index}")){
                        if($request->has("gallery_images_to_delete_of_color_{$index}")){
                            foreach($validated["gallery_images_to_delete_of_color_{$index}"] as $image){
                                delete_file("images/products/{$product->dir}/{$gallery_dir}/{$image}");
                            }
                        }
                        foreach($request->file("gallery_of_color_{$index}") as $key => $file){              
                            if($meta_gallery_of_color[$key]["status"] === "new"){
                                $file_name = $file->name();
                                $file_full_name = ($key + 1) . "_" . $file_name;
                                $file->save("images/products/{$product->dir}/{$gallery_dir}",$file_full_name);
                            }else{
                                $parts = explode("_",$file->name(),2);
                                $new_name = ($key + 1) . "_" . $parts[1];
                                rename_file("images/products/{$product->dir}/{$gallery_dir}/{$file->name()}","images/products/{$product->dir}/{$gallery_dir}/$new_name");
                            }
                        }
                    }
                }
                if(isset($colors_from_db[$index])){
                    DB::table("product_colors")->where("p_id",$product->id)->where("id",$colors_from_db[$index]->id)->update([
                        "color_image" => $color_image,
                        "color_name" => $color_name,
                        "price" => $validated["price_of_color_{$index}"] ?? 0,
                        "stock" => $validated["stock_of_color_{$index}"] ?? 0,
                        "gallery_dir" => $gallery_dir
                    ]);
                }else{
                    $color_id = DB::table("product_colors")->insert([
                        "p_id" => $product->id,
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
                    $colors_sizes_from_db = DB::table("product_colors_sizes")->where("p_id",$product->id)->where("color_id",$color_id)->get();
                    foreach($request->input("sizes_of_color_{$index}") as $size_index => $item){
                        if(isset($colors_sizes_from_db[$size_index])){
                            DB::table("product_colors_sizes")->where("id",$colors_sizes_from_db[$size_index]->id)->update([
                                "size" => $item['size'],
                                "price" => $item['price'],
                                "stock" => $item['stock']
                            ]);
                        }else{
                            DB::table("product_colors_sizes")->insert([
                                "p_id" => $product->id,
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
                            DB::table("product_colors_sizes")->where("id",$colors_sizes_from_db[$i]->id)->delete();
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
                    DB::table("product_colors_sizes")->where("p_id",$product->id)->where("color_id",$color_id)->delete();
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
            if($product->hasSizes()){
                DB::table("product_sizes")->where("p_id",$product->id)->delete();
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
            if($product->hasColorsSizes() || $product->hasColors()){
                $colors = DB::table("product_colors")->where("p_id",$product->id)->get();
                foreach($colors as $color){
                    remove_dir("images/products/{$product->dir}/{$color->gallery_dir}");
                    delete_file("{$color->color_image}");
                    DB::table("product_colors")->where("id",$color->id)->delete();
                }
            }
            if($request->has("sizes")){
                $sizes_from_db = DB::table("product_sizes")->where("p_id",$product->id)->get();
                foreach($request->input("sizes") as $index => $size){
                    if(isset($sizes_from_db[$index])){
                        DB::table("product_sizes")->where("id",$sizes_from_db[$index]->id)->where("p_id",$product->id)->update([
                            "size" => $size['value'],
                            "price" => $size['price'],
                            "stock" => $size['stock']
                        ]);
                    }else{
                        DB::table("product_sizes")->insert([
                            "p_id" => $product->id,
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
                        DB::table("product_sizes")->where("id",$sizes_from_db[$i]->id)->delete();
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
                DB::table("product_sizes")->where("p_id",$product->id)->delete();
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
        if(!$product->hasColorsSizes() && !$product->hasColors() && !$product->hasSizes()){
            $inventoryService->updateProductInventoryInStores(
                    $this,["variant" => false]
                );
        }
        if(!empty($validated["size_chart"])){
            $product->updateSizeChart($validated["size_chart"]);
        }else{
            $product->deleteSizeChart();
        }
        $product->save();
    }

    public function delete(Product $product) : void
    {
        remove_dir("images/products/{$product->dir}");
        $product->delete();
    }
}