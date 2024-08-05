<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Coupon;
use NhatHoa\Framework\Facades\Gate;

class CouponController extends Controller
{
    protected $couponModel;

    public function __construct(Coupon $coupon)
    {
        $this->couponModel = $coupon;
    }

    public function index()
    {
        if(!Gate::allows("read-coupon")){
            abort(401);
        }
        $coupons = $this->couponModel->getList();
        return view("admin/coupon/index",["coupons" => $coupons]);
    }

    public function add(Request $request)
    {
        if(!Gate::allows("create-coupon")){
            abort(401);
        }
        $validated = $request->validate([
            "code" => "bail|required|max:10|unique:coupons",
            "amount" => "bail|required|integer|min:10000",
            "minimum_spend" => "bail|required|integer|gt:amount",
            "usage" => "bail|required|integer|min:0",
            "per_user" => "bail|required|integer|min:0",
            "start_time" => "bail|required|date|date_format:d-m-Y H:i",
            "end_time" => "bail|required|date|date_format:d-m-Y H:i|after:start_time"
        ]);
        $this->couponModel->saveCoupon($validated);
        return response()->back()->with("success","Thêm mã giảm giá thành công");
    }

    public function edit($id)
    {
        $coupon = $this->couponModel->getCoupon($id);
        if(!$coupon){
            return;
        }
        return view("admin/coupon/edit",["coupon" => $coupon]);
    }

    public function update(Request $request,$id)
    {
        if(!Gate::allows("update-coupon")){
            abort(401);
        }
        $validated = $request->validate([
            "code" => "bail|required|max:10|unique:coupons,code,$id",
            "amount" => "bail|required|integer|min:10000",
            "minimum_spend" => "bail|required|integer|gt:amount",
            "usage" => "bail|required|integer|min:0",
            "per_user" => "bail|required|integer|min:0",
            "used" => "bail|required|integer|min:0",
            "start_time" => "bail|required|date|date_format:d-m-Y H:i",
            "end_time" => "bail|required|date|date_format:d-m-Y H:i|after:start_time"
        ]);
        $coupon = $this->couponModel->getCoupon($id);
        if(!$coupon){
            return;
        }
        $coupon->updateCoupon($validated);
        return response()->back()->with("success","Cập nhật mã giảm giá thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-coupon")){
            abort(401);
        }
        $this->couponModel->deleteCoupon($id);
        return response()->back()->with("success","Xóa mã giảm giá thành công");
    }
}
            