<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\User;
use NhatHoa\App\Models\Role;
use NhatHoa\Framework\Facades\Auth;
use NhatHoa\App\Middlewares\AdminAuth;
use NhatHoa\App\Repositories\Interfaces\UserRepositoryInterface;
use NhatHoa\App\Services\UserService;
use NhatHoa\App\Validations\UserValidation;
use NhatHoa\Framework\Facades\Gate;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request,UserService $userService)
    {
        if(!Gate::allows("read-user")) abort(401);
        if($request->has("role")){
            $role = $request->input("role");
        }elseif($request->has("unverified")){
            $unverified = true;
        }
        $currentPage = max((int) $request->query("page"),1);
        $keyword = $request->query("keyword"); 
        $number_map = array(
            "all" => User::count(),
            "admin" => $userService->countUser("administrator"),
            "cashier" => $userService->countUser("cashier"),
            "user" => $userService->countUser("user"),
            "unverified" => User::count(whereNull:array("email_verified_at","role_id"))
        );
        list($users,$total_users) = $this->userRepository->getAll($currentPage,10,$role??null,$keyword,$unverified??null);
        return view("admin/user/index",['users' =>$users,"total_orders"=>$total_users,"currentPage"=>$currentPage,"totalPages"=>ceil($total_users / 10),"number_map" => $number_map]);
    }

    public function profile($id)
    {
        $user = $this->userRepository->getById($id);
        if(!$user) return;
        $user->getProfile();
        return view("admin/user/profile",["user"=>$user]);
    }

    public function addView()
    {
        $roles = Role::all();
        return view("admin/user/add",["roles"=>$roles]);
    }

    public function addUser(Request $request, UserValidation $userValidation)
    {
        if(!Gate::allows("create-user")) abort(401);
        $validated = $userValidation->validateCreate($request);
        $this->userRepository->create($validated);
        return response()->back()->with("success","Thêm tài khoản thành công");
    }

    public function editView($id)
    {
        $user = $this->userRepository->getById($id);
        if(!$user) return;
        $roles = Role::all();
        return view("admin/user/edit",["user" => $user, "roles"=>$roles]);
    }

    public function update(Request $request, $id, UserValidation $userValidation)
    {
        if(!Gate::allows("update-user")) abort(401);
        $validated = $userValidation->validateUpdate($request,$id);
        $user = User::first(where:array("id"=>$id));
        if($user){
            $this->userRepository->update($user,$validated);
        }
        return response()->back()->with("success","Sửa tài khoản thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-user",$id)) abort(401);
        $user = User::first(where:array("id"=>$id));
        if(!$user) return false;
        $this->userRepository->delete($user);
        return response()->back()->with("success","Xóa tài khoản thành công");
    }

    public function addAddress(Request $request, UserValidation $userValidation)
    {
        $validated = $userValidation->validateCreateAddress($request);
        $user = Auth::user();
        $user->addAddress($validated);
        return redirect("user/addresses");
    }

    public function updateAddress(Request $request, $address_id, UserValidation $userValidation)
    {
        $validated = $userValidation->validateUpdateAddress($request);
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

    public function updateProfile(Request $request, UserValidation $userValidation)
    {
        $validated = $userValidation->validateUpdateProfile($request);
        $user = Auth::user();
        $user->updateProfile($validated);
        return response()->back()->with("success","Cập nhật thông tin cá nhân thành công");
    }

    public function updatePassword(Request $request, UserValidation $userValidation)
    {
        $validated = $userValidation->validateUpdatePassword($request);
        $user = User::first(where:array("id"=>Auth::user()->id));
        if(!password_verify($validated["oldpass"],$user->password)){
            return response()->back()->with("error","Mật khẩu hiện tại không đúng");
        }
        $user->password = password_hash($validated["newpass"],PASSWORD_DEFAULT);
        $user->save();
        return response()->back()->with("success","Đổi mật khẩu thành công");       
    }
}