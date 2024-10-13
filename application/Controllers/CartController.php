<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $data = array();
        if($request->session()->has("cart")){
            $data['cart'] = $this->cartService->getItems();
            $data['totalItems'] = $this->cartService->countTotalItems();
            $data['subtotal'] = $this->cartService->countSubtotal();
        }
        return view("client/cart",$data);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            "price" => "required|numeric",
            "p_id" => "required|exists:products,id",
            "p_name" => "required|string",
            "p_image" => "required|string"
        ]);
        $array = $this->cartService->addItem($validated,$request);
        if($array["status"] === false){
            return response()->json($array["message"],400);
        }
        if($request->has("buy_now")){
            return response()->json(["redirect" => url("checkout")],200);
        }
        return response()->json($array);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            "sign" => "required|in:+,-",
            "index" => "required"
        ]);
        $sign = $validated["sign"];
        $index = $validated["index"];
        if(!$request->session()->get("cart=>{$index}")) return;
        $array = $this->cartService->updateItem($sign,$index);
        if(isset($array["status"]) && $array["status"] === false){
            return response()->json($array["message"],400);
        }
        return response()->json($array);
    }

    public function delete($index)
    {
        $array = $this->cartService->deleteItem($index);
        return response()->json($array);
    }
}