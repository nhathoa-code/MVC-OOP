<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Repositories\Interfaces\CouponRepositoryInterface;
use NhatHoa\App\Validations\CouponValidation;
use NhatHoa\Framework\Facades\Gate;

class CouponController extends Controller
{
    protected $couponRepository;

    public function __construct(CouponRepositoryInterface $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }

    public function index()
    {
        if(!Gate::allows("read-coupon")) abort(401);
        $coupons = $this->couponRepository->getAll();
        return view("admin/coupon/index",["coupons" => $coupons]);
    }

    public function add(Request $request,CouponValidation $couponValidation)
    {
        if(!Gate::allows("create-coupon")) abort(401);
        $validated = $couponValidation->validateCreate($request);
        $this->couponRepository->create($validated);
        return response()->back()->with("success","Thêm mã giảm giá thành công");
    }

    public function edit($id)
    {
        $coupon = $this->couponRepository->getById($id);
        if(!$coupon) return;
        return view("admin/coupon/edit",["coupon" => $coupon]);
    }

    public function update(Request $request,$id,CouponValidation $couponValidation)
    {
        if(!Gate::allows("update-coupon")) abort(401);
        $coupon = $this->couponRepository->getById($id);
        if(!$coupon) return;
        $validated = $couponValidation->validateUpdate($request,$id);
        $this->couponRepository->update($coupon,$validated);
        return response()->back()->with("success","Cập nhật mã giảm giá thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-coupon")) abort(401);
        $coupon = $this->couponRepository->getById($id);
        if(!$coupon) return;
        $this->couponRepository->delete($coupon);
        return response()->back()->with("success","Xóa mã giảm giá thành công");
    }
}
            