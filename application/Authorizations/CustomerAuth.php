<?php

namespace NhatHoa\App\Authorizations;

use NhatHoa\App\Models\User;

class CustomerAuth
{
    public function create(User $user)
    {
        return $user->can("create","customer");
    }

    public function read(User $user)
    {
        return $user->can("read","customer");
    }

    public function update(User $user)
    {
        return $user->can("update","customer");
    }

    public function delete(User $user)
    {
        return $user->can("delete","customer");
    }
}