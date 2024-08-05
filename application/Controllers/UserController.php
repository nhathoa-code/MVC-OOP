<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\User;
use NhatHoa\App\Models\Role;
use NhatHoa\Framework\Facades\Auth;
use NhatHoa\App\Middlewares\AdminAuth;
use NhatHoa\Framework\Facades\Gate;

class UserController extends Controller
{
    protected $userModel;

    public function __construct(User $user)
    {
        $this->userModel = $user;
        $this->middleware(AdminAuth::class)->only(["addUser","update","delete"]);
    }

    public function index(Request $request)
    {
        if(!Gate::allows("read-user")){
            abort(401);
        }
        $limit = 10;
        $currentPage = $request->has("page") && is_numeric($request->input("page")) ? $request->input("page") : 1;
        $role = null;
        $keyword = null;
        $unverified = null;
        if($request->has("role")){
            $role = $request->input("role");
        }elseif($request->has("unverified")){
            $unverified = true;
        }
        if($request->has("keyword")){
            $keyword = $request->input("keyword");
        }
        $number_map = array(
            "all" => $this->userModel->count(),
            "admin" => $this->userModel->countUser("administrator"),
            "cashier" => $this->userModel->countUser("cashier"),
            "user" => $this->userModel->countUser("user"),
            "unverified" => $this->userModel->count(whereNull:array("email_verified_at","role_id"))
        );
        list($users,$total_users) = $this->userModel->getList($currentPage, $limit, $role, $keyword, $unverified);
        return view("admin/user/index",['users' =>$users,"total_orders"=>$total_users,"currentPage"=>$currentPage,"totalPages"=>ceil($total_users / $limit),"number_map" => $number_map]);
    }

    public function profile($id)
    {
        $user = $this->userModel->getUser($id);
        if(!$user){
            return;
        }
        $user->getProfile();
        return view("admin/user/profile",["user"=>$user]);
    }

    public function addView()
    {
        $roles = Role::all();
        return view("admin/user/add",["roles"=>$roles]);
    }

    public function addUser(Request $request)
    {
        if(!Gate::allows("create-user")){
            abort(401);
        }
        $validated = $request->validate([
            "name" => "required",
            "login_key" => "bail|required|unique:users",
            "email" => "bail|required|email|unique:users",
            "password" => "bail|required|regex:/^[^\s]{6,}$/",
            "role" => "bail|nullable|required|exists:roles,id"
        ]);
        $this->userModel->addUser($validated);
        return response()->back()->with("success","Thêm tài khoản thành công");
    }

    public function editView($id)
    {
        $user = $this->userModel->getUser($id);
        if(!$user){
            return;
        }
        $roles = Role::all();
        return view("admin/user/edit",["user" => $user, "roles"=>$roles]);
    }

    public function update(Request $request,$id)
    {
        if(!Gate::allows("update-user")){
            abort(401);
        }
        $validated = $request->validate([
            "name" => "required",
            "login_key" => "bail|required|unique:users,login_key,$id",
            "email" => "bail|required|email|unique:users,email,$id",
            "password" => "bail|nullable|required|regex:/^[^\s]{6,}$/",
            "role" => "bail|nullable|required|exists:roles,id"
        ]);
        $user = $this->userModel->first(where:array("id"=>$id));
        if($user){
            $user->updateUser($validated);
        }
        return response()->back()->with("success","Sửa tài khoản thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-user",$id)){
            abort(401);
        }
        $this->userModel->deleteUser($id);
        return response()->back()->with("success","Xóa tài khoản thành công");
    }

    public function addAddress(Request $request)
    {
        $validated = $request->validate([
            "name" => "required",
            "phone" => "bail|required|regex:/^0[0-9]{9}$/",
            "province" => "required",
            "province_id" => "bail|required|numeric",
            "district" => "required",
            "district_id" => "bail|required|numeric",
            "ward" => "required",
            "ward_code" => "bail|required|numeric",
            "address" => "required"
        ]);
        $user = Auth::user();
        $user->addAddress($validated);
        return redirect("user/addresses");
    }

    public function updateAddress(Request $request,$address_id)
    {
        $validated = $request->validate([
            "name" => "required",
            "phone" => "bail|required|regex:/^0[0-9]{9}$/",
            "province" => "required",
            "province_id" => "bail|required|numeric",
            "district" => "required",
            "district_id" => "bail|required|numeric",
            "ward" => "required",
            "ward_code" => "bail|required|numeric",
            "address" => "required"
        ]);
        $user = Auth::user();
        $user->updateAddress($validated,$address_id);
        return redirect("user/addresses");
    }

    public function deleteAddress($address_id)
    {
        $user = Auth::user();
        $user->deleteAddress($address_id);
        redirect("user/addresses");
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            "name" => "required",
            "birth_day" => "bail|required|regex:/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/",
            "phone" => "bail|required|regex:/^0[0-9]{9}$/",
            "province" => "required",
            "province_id" => "bail|required|numeric",
            "district" => "required",
            "district_id" => "bail|required|numeric",
            "ward" => "required",
            "ward_code" => "bail|required|numeric",
            "gender" => "bail|required|in:boy,girl"
        ]);
        $user = Auth::user();
        $user->updateProfile($validated);
        return response()->back()->with("success","Cập nhật thông tin cá nhân thành công");
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            "oldpass" => "required",
            "newpass" => "bail|required|regex:/^[^\s]{6,}$/",
            "retype_newpass" => "bail|required|same:newpass"
        ]);
        $user = Auth::user()->getUser(Auth::user()->id);
        if(!password_verify($validated["oldpass"],$user->password)){
            return response()->back()->with("error","Mật khẩu hiện tại không đúng");
        }
        $user->password = password_hash($validated["newpass"],PASSWORD_DEFAULT);
        $user->save();
        return response()->back()->with("success","Đổi mật khẩu thành công");       
    }
}