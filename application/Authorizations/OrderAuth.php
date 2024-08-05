<?php

namespace NhatHoa\App\Authorizations;

use NhatHoa\App\Models\User;

class OrderAuth
{
    public function read(User $user)
    {
        return $user->can("read","order");
    }

    public function update(User $user)
    {
        return $user->can("update","order");
    }

    public function delete(User $user)
    {
        return $user->can("delete","order");
    }
}