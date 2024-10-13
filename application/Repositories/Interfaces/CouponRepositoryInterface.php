<?php

namespace NhatHoa\App\Repositories\Interfaces;
use NhatHoa\App\Models\Coupon;

interface CouponRepositoryInterface
{
    public function getAll() : array;
    public function getById(int $id) : Coupon|null;
    public function getByCode(string $code) : Coupon|null;
    public function create(array $data) : void;
    public function update(Coupon $coupon, array $data) : void;
    public function delete(Coupon $coupon) : void; 
}