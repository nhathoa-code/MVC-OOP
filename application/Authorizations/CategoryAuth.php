<?php

namespace NhatHoa\App\Authorizations;

use NhatHoa\App\Models\User;

class CategoryAuth
{
    public function create(User $user)
    {
        return $user->can("create","category");
    }

    public function read(User $user)
    {
        return $user->can("read","category");
    }

    public function update(User $user)
    {
        return $user->can("update","category");
    }

    public function delete(User $user)
    {
        return $user->can("delete","category");
    }
}