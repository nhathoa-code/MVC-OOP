<?php

namespace NhatHoa\App\Authorizations;

use NhatHoa\App\Models\User;

class PermissionAuth
{
    public function create(User $user)
    {
        return $user->can("create","permission");
    }

    public function read(User $user)
    {
        return $user->can("read","permission");
    }

    public function update(User $user)
    {
        return $user->can("update","permission");
    }

    public function delete(User $user)
    {
        return $user->can("delete","permission");
    }
}