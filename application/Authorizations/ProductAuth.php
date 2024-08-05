<?php

namespace NhatHoa\App\Authorizations;

use NhatHoa\App\Models\User;

class ProductAuth
{
    public function create(User $user)
    {
        return $user->can("create","product");
    }

    public function read(User $user)
    {
        return $user->can("read","product");
    }

    public function update(User $user)
    {
        return $user->can("update","product");
    }

    public function delete(User $user)
    {
        return $user->can("delete","product");
    }
}