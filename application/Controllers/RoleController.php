<?php

namespace NhatHoa\App\Controllers;

use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\Role;
use NhatHoa\App\Repositories\Interfaces\PermissionGroupRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\RoleRepositoryInterface;
use NhatHoa\Framework\Facades\Gate;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function index()
    {
        if(!Gate::allows("read-role")) abort(401);
        $roles = array_map(function($role){
            $role->permissions = $role->formatPermissions($role->permissions);
            return $role;
        },$this->roleRepository->getAll());
        return view("admin/role/index",["roles"=>$roles]);
    }

    public function addView(PermissionGroupRepositoryInterface $permissionGroupRepository)
    {
        $permission_groups = $permissionGroupRepository->getAll();
        return view("admin/role/add",["permission_groups"=>$permission_groups]);
    }

    public function add(Request $request)
    {
        if(!Gate::allows("create-role")) abort(401);
        $validated = $request->validate([
            "name" => "bail|required|string|min:3|unique:roles",
            "permissions" => "nullable|array|exists:permissions,id"
        ]);
        $this->roleRepository->create($validated);
        return response()->redirect("admin/role/list")
                        ->with("success","Thêm vai trò thành công");
    }

    public function edit($id,PermissionGroupRepositoryInterface $permissionGroupRepository)
    {
        $role = $this->roleRepository->getById($id);
        if(!$role) return;
        $permission_groups = $permissionGroupRepository->getAll();
        return view("admin/role/edit",["role"=>$role,"permission_groups"=>$permission_groups]);
    }

    public function update(Request $request,$id)
    {
        if(!Gate::allows("update-role")) abort(401);
        $validated = $request->validate([
            "name" => "bail|required|string|min:3|unique:roles,name,$id",
            "permissions" => "required|array|exists:permissions,id"
        ]);
        $role = Role::first(where:array("id"=>$id));
        if(!$role) return;
        $this->roleRepository->update($role,$validated);
        return response()->redirect("admin/role/list")
                        ->with("success","Cập nhật vai trò thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-role")) abort(401);
        $role = Role::first(where:array("id"=>$id));
        if(!$role) return;
        $this->roleRepository->delete($role);
        return response()->redirect("admin/role/list")
                        ->with("success","Xóa vai trò thành công");
    }
}