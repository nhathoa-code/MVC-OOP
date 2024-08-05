<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\User;
use NhatHoa\App\Models\Order;
use NhatHoa\App\Services\EmailService;
use NhatHoa\Framework\Event;
use NhatHoa\Framework\Facades\Auth;
use NhatHoa\Framework\Facades\DB;

class AuthController extends Controller
{
    protected $userModel;

    public function __construct(User $user)
    {
        $this->userModel = $user;
    }

    public function loginView()
    {
        if(Auth::check()){
           return redirect("user/member");
        }
        return view("client/auth/login");
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            "login_key" => "bail|required|email",
            "password" => "required"
        ]);
        $array = $this->userModel->login($validated["login_key"],$validated["password"]);
        if($array["status"]){
            Auth::login($array["user"]);
            return redirect("user/member");
        }else{
            return response()->back()->with("error",$array["message"]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->back();
    }

    public function registryView()
    {
        return view("client/auth/registry");
    }

    public function registry(Request $request,EmailService $emailService)
    {
        $validated = $request->validate([
            "name" => "bail|required|min:3",
            "email" => "bail|required|email|unique:users",
            "password" => "bail|required|regex:/^[0-9A-Za-z]{6,}$/"
        ]);
        $verify_token = $this->userModel->register($validated);
        $href = url("auth/email/verify?token={$verify_token}");
        $message = "<a href='{$href}'>Vui lòng click vào đây để xác thực tài khoản</a>";
        Event::dispatch("email",[$emailService,$validated["email"],"Xác thực tài khoản",$message]);
        return response()->back()->with("success","Đăng ký thành công, vui lòng xác thực tài khoản qua email");
    }

    public function user($part)
    {
        $data = array();
        $user = Auth::user();
        switch($part){
            case "member":
                $data['user_rank'] = $user->getMeta("rank");
                $data['user_point'] = $user->getMeta("point");
                $data['user_total_spend'] = $user->getMeta("total_spend");
                $data['point_history'] = $user->getPointHistory();
                break;
            case "profile":
                $data['profile'] = $user->getMeta("profile");
                break;
            case "addresses":
                $data['addresses'] = $user->getMeta("address",true); 
                break;
            case "address/edit":
                if(get_query("id")){
                    $address = $user->getMeta("address",meta_id:get_query("id"),full:true);
                    if(!$address){
                        redirect("user/addresses");
                    }
                    $data['address'] = unserialize($address->meta_value);
                    $data['id'] = $address->id;
                }else{
                    redirect("user/addresses");
                }
                break;
            case "orders":
                $number_map = array(
                    "all" => $user->countOrders(),
                    "pending" => $user->countOrders("pending"),
                    "toship" => $user->countOrders("toship"),
                    "shipping" => $user->countOrders("shipping"),
                    "completed" => $user->countOrders("completed"),
                    "cancelled" => $user->countOrders("cancelled"),
                );
                $data['status_map'] = (new Order)->getMapStatus();
                $data['number_map'] = $number_map;
                $status = get_query("status");
                if($status){
                    $data['status'] = $status;
                    $data['orders'] = $user->getOrders($status);
                }else{
                    $data['orders'] = $user->getOrders();
                }
                break;
            case "order":
                if(get_query("id"))
                {
                    $order_id = get_query("id");
                    $order = $user->getOrder($order_id);
                    if($order){
                        $data['order_meta'] = array();
                        $data['status_map'] = $order->getMapStatus();
                        $data['order'] = $order->getOrder($order->id);
                        $order_meta = $order->getMetas();
                        if(!empty($order_meta)){
                            $order_meta = array_map(function($item){
                                return [$item->meta_key => $item->meta_value];
                            },$order_meta);
                            $data['order_meta'] = array_merge(...$order_meta);
                        }
                    }
                }
                break;
            case "wishlist":
                $products = array_map(function($item){
                    $item->colors = $item->getColors();
                    $item->thumbnail = getFiles("images/products/{$item->dir}/product_images")[0];
                    return $item;
                },Auth::user()->getWishList());
                $data['products'] = $products;
                break;
            case "coupons":
                $data['coupons'] = DB::table("coupons")->get();
                break;
        }
        return view("client/auth/user",["part" => $part,"data" => $data]);
    }

    public function forgotPassword()
    {
        return view("client/auth/forgot");
    }

    public function retrievePassword(Request $request,EmailService $emailService)
    {
        $validated = $request->validate([
            "email" => "bail|required|email|exists:users",
        ]);
        $email = $validated["email"];
        $verify_token = generateToken();
        DB::table("password_resets")->insert([
            "email" => $email,
            "token" => $verify_token
        ]);
        $href = url("auth/resetpassword?token={$verify_token}");
        $message = "<a href='{$href}'>Vui lòng click vào đây để lấy lại mật khẩu</a>";
        Event::dispatch("email",[$emailService,$email,"Lấy lại mật khẩu",$message]);
        return response()->back()->with("success","Link lấy lại mật khẩu đã được gửi đến email");
    }

    public function resetPassword(Request $request)
    {
        $token = $request->input("token");
        if(!$token){
            return;
        }
        $email = DB::table("password_resets")->where("token",$token)->first("email");
        if(!$email){
            return;
        }
        $validated = $request->validate([
            "password" => "bail|required|regex:/^[^\s]{6,}$/",
            "retype_password" => "bail|required|same:password"
        ]);
        $user = User::first(where:array("email"=>$email));
        $user->password = password_hash($validated["password"],PASSWORD_DEFAULT);
        $user->save();
        DB::table("password_resets")
            ->where("email",$email)
            ->where("token",$token)
            ->limit(1)
            ->delete();
        return response()->redirect("auth/login")->with("success","Đặt lại mật khẩu thành công");
    }

    public function resetPasswordView()
    {
        $token = get_query("token");
        if(!$token){
            return "Không có token";
        }
        if(!DB::table("password_resets")->where("token",$token)->first()){
            return "Token không tồn tại";
        }
        return view("client/auth/reset");
    }

    public function authVerify()
    {
        $token = get_query("token");
        if($token){
            $user_id = DB::table("user_meta")
                        ->where("meta_key","verify_token")
                        ->where("meta_value",$token)
                        ->first("user_id");
            if($user_id){
                $user = User::first(where:array("id"=>$user_id));
                $user->email_verified_at = now();
                $user->save();
                $user->deleteMeta("verify_token");
                $user->setUpMeta();
                return "Email verified successfully";
            }
        }
        return "invalid token";
    }
}