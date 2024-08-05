<?php

namespace NhatHoa\App\Middlewares;
use NhatHoa\Framework\Abstract\Middleware;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Facades\Auth;
use NhatHoa\Framework\Facades\Gate;

class Admin implements Middleware
{
    public function handle(Request $request)
    {
        if(Auth::check("admin")){
            return $request;
        }
        if($request->isAjax()){
            return response()->json("Unauthorized",401);
        }
        return redirect("admin/login");
    }
}