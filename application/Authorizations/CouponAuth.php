<?php

namespace NhatHoa\App\Authorizations;

use NhatHoa\App\Models\User;

class CouponAuth
{
    public function create(User $user)
    {
        return $user->can("create","coupon");
    }

    public function read(User $user)
    {
        return $user->can("read","coupon");
    }

    public function update(User $user)
    {
        return $user->can("update","coupon");
    }

    public function delete(User $user)
    {
        return $user->can("delete","coupon");
    }
}