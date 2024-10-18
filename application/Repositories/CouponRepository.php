<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\App\Models\Coupon;
use NhatHoa\App\Repositories\Interfaces\CouponRepositoryInterface;
use NhatHoa\App\Repositories\BaseRepository;

class CouponRepository extends BaseRepository implements CouponRepositoryInterface
{
    public function getAll() : array
    {
        return Coupon::all(orderBy:array("id"=>"desc"));
    }

    public function getById($id) : Coupon|null
    {
        return Coupon::first(where:array("id" => $id));
    }

    public function getByCode($code) : Coupon|null
    {
        return Coupon::first(where:array("code"=>$code));
    }

    public function create($validated) : void
    {
        $coupon = new Coupon();
        $coupon->code = $validated["code"];
        $coupon->amount = $validated["amount"];
        $coupon->minimum_spend = $validated["minimum_spend"];
        $coupon->coupon_usage = $validated["usage"];
        $coupon->per_user = $validated["per_user"];
        $coupon->start_time = \DateTime::createFromFormat("d-m-Y H:i", $validated["start_time"])->format("Y-m-d H:i");
        $coupon->end_time = \DateTime::createFromFormat("d-m-Y H:i", $validated["end_time"])->format("Y-m-d H:i");
        $coupon->save();
    }

    public function update(Coupon $coupon, $validated) : void
    {
        $coupon->code = $validated["code"];
        $coupon->amount = $validated["amount"];
        $coupon->minimum_spend = $validated["minimum_spend"];
        $coupon->coupon_usage = $validated["usage"];
        $coupon->per_user = $validated["per_user"];
        $coupon->start_time = \DateTime::createFromFormat("d-m-Y H:i", $validated["start_time"])->format("Y-m-d H:i");
        $coupon->end_time = \DateTime::createFromFormat("d-m-Y H:i", $validated["end_time"])->format("Y-m-d H:i");
        $coupon->save();
    }

    public function delete(Coupon $coupon) : void
    {
        $coupon->delete();
    }
}