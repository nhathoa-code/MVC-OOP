<?php

namespace NhatHoa\App\Authorizations;

use NhatHoa\App\Models\User;

class BeforeAuth
{
    public function authorize(User $user)
    {
        return $user->role == "administrator";
    }
}