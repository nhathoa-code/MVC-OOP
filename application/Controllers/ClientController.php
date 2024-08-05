<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Category;
use NhatHoa\App\Models\Coupon;
use NhatHoa\App\Models\Order;
use NhatHoa\App\Models\Product;
use NhatHoa\App\Models\Province;
use NhatHoa\App\Services\CartService;
use NhatHoa\App\Services\EmailService;
use NhatHoa\App\Services\InventoryService;
use NhatHoa\App\Services\Factories\PaymentFactory;
use NhatHoa\Framework\Event;
use NhatHoa\Framework\Facades\Auth;
use NhatHoa\Framework\Facades\DB;

class ClientController extends Controller
{
    protected $categoryModel;
    protected $productModel;

    public function __construct(Category $category,Product $product)
    {
        $this->categoryModel = $category;
        $this->productModel = $product;
    }

    public function index(Order $order)
    {
        $data['categories'] = $this->categoryModel->all(whereNull:array("parent_id"));
        $data['latest_products'] = array_map(function($item){
            $item->colors = $item->getColors();
            $item->thumbnail = getFiles("images/products/{$item->dir}/product_images")[0];
            return $item;
        },$this->productModel->getLatest(8));
        $top_saled_products = array_map(function($item){
            return new Product(DB::table("products")->where("id",$item->id)->first());
        },$order->getTopSaled(10));
        $data["top_saled_products"] = array_map(function($item){
            $item->colors = $item->getColors();
            $item->thumbnail = getFiles("images/products/{$item->dir}/product_images")[0];
            return $item;
        },$top_saled_products);
        return view("client/index",$data);
    }

    public function collection(Request $request,$categories)
    {
        $limit = isMobileDevice() ? 8 : 9;
        $collection = array();
        $category_id = $this->categoryModel->getLastIdFromUrl($categories);
        if($category_id){
            $page = (int) $request->query("page");
            $page = $page > 0 ? $page : 1;
            list($collection,$number_of_products,$total_pages) = $this->productModel->filter($request,$category_id,$limit,$page);
            $total_pages = ceil($number_of_products / $limit);
            $collection = array_map(function($item) use($request){
                if($request->isAjax()){
                    $item->colors = array_map(function($item){
                        $item->color_image = url($item->color_image);
                        return $item;
                    },$item->getColors());
                    $item->product_url = url("product/detail/{$item->id}");
                }else{
                    $item->colors = $item->getColors();
                }
                $item->thumbnail = getFiles("images/products/{$item->dir}/product_images")[0];
                return $item;
            },$collection);
            if($request->isAjax()){
                return response()->json(["collection"=>$collection,"total_pages"=>$total_pages,"number_of_products"=>$number_of_products]);
            }else{
                $sizes_filter = DB::table("product_categories as pc")
                                        ->select(["pcs.size"])
                                        ->leftJoin("products as p","p.id","=","pc.p_id")
                                        ->leftJoin("product_sizes as ps","ps.p_id","=","p.id")
                                        ->leftJoin("product_colors_sizes as pcs","pcs.p_id","=","p.id")
                                        ->where("cat_id",$category_id)
                                        ->whereNotNull("pcs.size")
                                        ->distinct()
                                        ->get();
                return view("client/product/collection",
                    [
                        "collection"=>$collection,
                        "collection_url"=> url("collection/{$categories}"),
                        "total_pages" => $total_pages,
                        "number_of_products"=>$number_of_products,
                        "limit"=>$limit,
                        "page"=>$page,
                        "displayed_products"=>count($collection),
                        "sizes_filter"=>$sizes_filter
                    ]
                );             
            }
        }
    }

    public function search(Request $request)
    {
        $keyword = get_query("keyword");
        if($keyword){
            $limit = 8;
            $page = (int) $request->query("page");
            $page = $page > 0 ? $page : 1;
            list($collection,$number_of_products,$total_pages) = $this->productModel->search($request,$keyword,$page,$limit);
            $collection = array_map(function($item) use($request){
                if($request->isAjax()){
                    $item->colors = array_map((function($item){
                        $item->color_image = url($item->color_image);
                        return $item;
                    }),$item->getColors());
                    $item->product_url = url("product/detail/{$item->id}");
                }else{
                    $item->colors = $item->getColors();
                }
                $item->thumbnail = getFiles("images/products/{$item->dir}/product_images")[0];
                return $item;
            },$collection);
            if($request->isAjax()){
                return response()->json(["collection"=>$collection]);
            }else{
                return view("client/search",
                    [
                        "collection" => $collection,
                        "total_pages" => $total_pages,
                        "number_of_products" => $number_of_products,
                        "limit" => $limit,
                        "page" => $page,
                        "displayed_products" => count($collection)
                    ]);
            }
        }
    }

    public function searchStores(Request $request)
    {
        $product = $request->query("product");
        $color = $request->get("color");
        $size = $request->get("size");
        $province = $request->get("province");
        $district = $request->get("district");
        $query = DB::table("inventory as i")
                    ->where("product_id",$product);
        if($color){
            $query->where("color_id",$color);
        }
        if($size){
            $query->where("size",$size);
        }
        $query->join("stores as t","t.id","=","i.store_id");
        if($province){
            $query->where("t.province_id",$province);
        }
        if($district){
            $query->where("t.district_id",$district);
        }
        $query->where("i.stock",">",0);
        $query->join("provinces as p","p.id","=","t.province_id")
                ->join("province_districts as pd","pd.id","=","t.district_id")
                ->groupBy("t.id")
                ->select(["t.*","p.name as province","pd.name as district,i.stock"]);
        $available_stores = $query->distinct()->get();
        return response()->json($available_stores);
    }

    public function product(Request $request,$id,Province $province)
    {   
        $data = array();
        $product = $this->productModel->getProduct($id);
        if(!$product){
            return;
        }
        $data['product'] = $product;
        if(Auth::check()){
            if(Auth::user()->hasWishList($product->id)){
                $product->wl = true;
            }
        }
        if(!$request->session()->has("recent_viewed_products=>{$product->id}")){
            $request->session()->push("recent_viewed_products",$product,$product->id);
        }
        $data['recent_viewed_products'] = array_filter($request->session()->get("recent_viewed_products"),function($item) use($product){
            return $item->id !== $product->id;
        });
        $data["related_products"] = array_map(function($item){
            $item->colors = $item->getColors();
            $item->thumbnail = getFiles("images/products/{$item->dir}/product_images")[0];
            return $item;
        },$product->getRelated(10));
        $provinces = array_map(function($item){
            $item->districts = $item->getDistricts();
            return $item;
        },$province->all());
        $data["provinces"] = $provinces;
        return view("client/product/detail",$data);
    }

    public function checkoutView(Request $request,CartService $cartService)
    {
        $data = array();
        if(!$request->session()->has("cart") || count($request->session()->get("cart")) <= 0){
            redirect("cart");
        }
        if($request->session()->has("cart")){
            $data['cart'] = $cartService->getItems("cart");
            $data['totalItems'] = $cartService->countTotalItems();
            $data['subtotal'] = $cartService->countSubtotal();
        }
        if(login()){
            $user = Auth::user();
            $data['addresses'] = $user->getMeta("address",true);
            $data['v_point'] = $user->getMeta("point");
        }
        return view("client/checkout",$data);
    }

    public function checkout(Request $request,CartService $cartService,InventoryService $inventoryService,Coupon $coupon,EmailService $emailService)
    {
        if(!$request->session()->has("cart") || empty($request->session()->get("cart"))){
            return redirect("cart");
        }
        $validated = $request->validate([
            "name" => "required",
            "email" => "bail|required|email",
            "phone" => "bail|required|regex:/^0[0-9]{9}$/",
            "province" => "required",
            "district" => "required",
            "ward" => "required",
            "address" => "required",
            "payment_method" => "bail|required|in:cod,vnpay",
            "shipping_fee" => "bail|required|numeric",
            "v_point" => "bail|nullable|required|integer|min:1"
        ],[
            "phone.regex" => "Điện thoại không hợp lệ"
        ]);
        foreach($cartService->getItems() as $index => $item){
            $array = $inventoryService->checkStock($item["p_id"],$item["p_name"],$item["color_id"] ?? null,$item["color_name"] ?? null,$item["size"] ?? null,$item["quantity"]);
            if($array["status"]){
                $request->session()->set("cart=>{$index}=>stock",$array["stock"]);
            }else{
                return response()->back()->with("message",$array["message"]);
            }
        }
        $order_total = $cartService->countSubtotal(); 
        $email = Auth::check() ? Auth::user()->email : $validated["email"];
        $name = $validated["name"];
        $address = array("ward"=> $validated["ward"],"district"=>$validated["district"],"city"=>$validated["province"],"address"=>$validated["address"]);
        $phone = $validated["phone"];
        $payment_method = $validated["payment_method"];
        $data = [
            "user_id" => login() ? getUser()->id : null,
            "total" => $order_total,
            "payment_method" => $payment_method,
            "status" => "pending",
            "shipping_fee" => $validated["shipping_fee"],
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "address" => json_encode($address),
            "note" => $validated["note"]
        ];
        if($request->has("coupon")){
            if(!login()){
                return response()->back()->with("message","Đăng nhập để có thể áp dụng mã giảm giá");
            }
            $coupon_code = $validated["coupon"];
            $Coupon = $coupon->getCouponByCode($coupon_code);
            if($Coupon){
                if(!login()){
                    return response()->back()->with("message","Đăng nhập để có thể áp dụng mã giảm giá");
                }
                $array = $Coupon->apply(Auth::user()->id,$cartService->countSubtotal());
                if($array["status"] === false){
                    return response()->back()->with("message",$array["message"]);
                }else{
                    $data['coupon'] = $Coupon->amount;
                }
            }else{
                return response()->back()->with("message","Mã không tồn tại!");
            }
        }
        if($request->has("v_point")){
            if(!login()){
                return response()->back()->with("message","vui lòng đăng nhập trước");
            }
            $point = $validated["v_point"];
            $point_from_db = (int) Auth::user()->getMeta("point");
            if($point_from_db){
                if($point > $point_from_db){
                    return response()->back()->with("message","Số dư v-point không khả dụng!");
                }
                $V_point = $point;
            }
        }
        $order_id =  date('d') . date('m') . explode(".",number_format(microtime(true), 6))[1];
        $data['id'] = $order_id;
        $order = new Order;
        $order->saveOrder($data,$Coupon ?? null,$V_point ?? null,$cartService,$inventoryService);
        Event::dispatch("order-email",[$order,$emailService,"VNH - Xác nhận đơn hàng","confirm_order"]);
        $cartService->reset();
        $order->processPayment(PaymentFactory::get($payment_method));
        return view("client/success",["order_id"=>$order_id]);
    }

    public function applyCoupon(Request $request,Coupon $coupon,CartService $cartService)
    {
        $validated = $request->validate([
            "coupon_code" => "required"
        ]);
        $coupon_code = $validated["coupon_code"];
        $coupon = $coupon->getCouponByCode($coupon_code);
        if($coupon){
            if(!login()){
                return response()->json("Đăng nhập để có thể áp dụng mã giảm giá",400);
            }
            $array = $coupon->apply(Auth::user()->id,$cartService->countSubtotal());
            if($array["status"] === false){
                return response()->json($array["message"],400);
            }else{
                return response()->json($array);
            }
        }else{
            return response()->json("Mã không tồn tại!",400);
        }
    }

    public function applyPoint(Request $request)
    {
        $validated = $request->validate([
            "point" => "bail|required|integer|min:1"
        ]);
        $point = $validated["point"];
        if(!login()){
            return response()->json("vui lòng đăng nhập trước",400);
        }
        $point_from_user = Auth::user()->getMeta("point");
        if($point_from_user){
            if($point > $point_from_user){
               return response()->json("Số dư không khả dụng!",400);
            }
            return response()->json(["point" => $point,"message"=>"Áp dụng v-point thành công"]);
        }
    }

    public function orderTrack(Order $order)
    {
        $data = array();
        $order_id = get_query("order_id");
        if($order_id){
           if(preg_match("/^[0-9]{10}$/",$order_id)){
                $order = $order->getOrder($order_id);
                if($order){
                    $data['status_map'] = $order->getMapStatus();
                    $data['order'] = $order;
                }else{
                    $data['not_found'] = true;
                }
           }else{
                $data["message"] = "Mã đơn hàng không hợp lệ";
           }
        }
        return view("client/order_track",$data);
    }

    public function vnpayConfirm(Request $request,Order $order)
    {
        $vnp_HashSecret = "HOBQFVGFSTRKIMJOTRJRIBQIZOUVTBWI";
        $vnp_SecureHash = $_GET['vnp_SecureHash'];
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                $order_id = $request->query("vnp_TxnRef");
                $Order = $order->first(where:array("id"=>$order_id));
                if($Order->paid_status === "Đã thanh toán | MGD: " . $request->query("vnp_TransactionNo")){
                    return "Thanh toán đã được xử lý";
                }
                $Order->paid_status = "Đã thanh toán | MGD: " . $request->query("vnp_TransactionNo");
                $Order->save();
                return view("client/success",["order_id"=>$order_id]);
            }
        } 
    }

    public function notFound()
    {
        return view("client/not_found");
    }
}