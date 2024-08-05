<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Services\WishListService;
use NhatHoa\Framework\Facades\Auth;

class WishListController extends Controller
{
    protected $_wishListService;

    public function __construct(WishListService $wishListService)
    {
        $this->_wishListService = $wishListService;
    }

    public function add(Request $request)
    {
        if(!Auth::check()){
            return response()->json("Vui lòng đăng nhập trước!",400);
        }
        $validated = $request->validate([
            "p_id" => "required|exists:products,id"
        ]);
        $this->_wishListService->add($validated["p_id"]);
        return response()->json("Đã lưu sản phẩm vào wishlist");
    }

    public function remove(Request $request)
    {
        if(!Auth::check()){
            return response()->json("Vui lòng đăng nhập trước!",400);
        }
        $validated = $request->validate([
            "p_id" => "required|exists:products,id"
        ]);
        $this->_wishListService->remove($validated["p_id"]);
        return response()->json("Đã xóa sản phẩm khỏi wishlist");
    }
}