<?php

namespace NhatHoa\App\Authorizations;

use NhatHoa\App\Models\User;

class RoleAuth
{
    public function create(User $user)
    {
        return $user->can("create","role");
    }

    public function read(User $user)
    {
        return $user->can("read","role");
    }

    public function update(User $user)
    {
        return $user->can("update","role");
    }

    public function delete(User $user)
    {
        return $user->can("delete","role");
    }
}