<?php

namespace NhatHoa\App\Authorizations;

use NhatHoa\App\Models\User;

class UserAuth
{
    public function create(User $user)
    {
        return $user->can("create","user");
    }

    public function read(User $user)
    {
        return $user->can("read","user");
    }

    public function update(User $user)
    {
        return $user->can("update","user");
    }

    public function delete(User $user,$id)
    {
        if($user->can("delete","user") && $user->id != $id){
            return true;
        }
        return false;
    }
}