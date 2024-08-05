<?php

namespace NhatHoa\App\Middlewares;
use NhatHoa\Framework\Abstract\Middleware;
use NhatHoa\Framework\Core\Request;

class CSRF implements Middleware
{
    public function handle(Request $request) 
    {
        if (!$request->session()->has('csrf_token')) {
            $request->session()->set('csrf_token',bin2hex(random_bytes(32)));
        }
        if ($request->method() === "POST") {
            $submittedToken = $request->post('csrf_token') ?? '';
            if (!hash_equals($request->session()->get('csrf_token'), $submittedToken)) {
                if($request->isAjax()){
                    return response()->json(["message"=>"CSRF token mismatch"],403);
                }
                throw new \Exception("CSRF token mismatch",403);
            }
        }
        return $request;
    }
}