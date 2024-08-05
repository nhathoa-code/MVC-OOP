<?php

namespace NhatHoa\App\Authorizations;

use NhatHoa\App\Models\User;

class StoreAuth
{
    public function create(User $user)
    {
        return $user->can("create","store");
    }

    public function read(User $user)
    {
        return $user->can("read","store");
    }

    public function update(User $user)
    {
        return $user->can("update","store");
    }

    public function delete(User $user)
    {
        return $user->can("delete","store");
    }
}