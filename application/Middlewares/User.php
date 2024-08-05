<?php

namespace NhatHoa\App\Middlewares;
use NhatHoa\Framework\Abstract\Middleware;
use NhatHoa\Framework\Facades\Auth;
use NhatHoa\Framework\Core\Request;

class User implements Middleware
{
    public function handle(Request $request)
    {
        if(Auth::check()){
            return $request;
        }
        return redirect("auth/login");
    }
}