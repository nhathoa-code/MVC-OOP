<?php

namespace NhatHoa\App\Middlewares;
use NhatHoa\Framework\Abstract\Middleware;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Facades\Auth;

class AdminAuth implements Middleware
{
    public function handle(Request $request)
    {
        if(Auth::user("admin")->role !== "administrator"){
            if($request->isAjax()){
                return response()->json("Bạn không có quyền này",401);
            }
            return response()->back()->with("error","Bạn không có quyền này");
        }
        return $request;
    }
}