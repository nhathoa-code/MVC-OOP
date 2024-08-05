<?php

namespace NhatHoa\App\Controllers;

use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\PermissionGroup;
use NhatHoa\App\Models\Role;
use NhatHoa\Framework\Facades\Gate;

class RoleController extends Controller
{
    protected $roleModel;
    protected $permissionGroupModel;

    public function __construct(Role $role,PermissionGroup $permission_group)
    {
        $this->roleModel = $role;
        $this->permissionGroupModel = $permission_group;
    }

    public function index()
    {
        if(!Gate::allows("read-role")){
            abort(401);
        }
        $roles = $this->roleModel->getAll();
        return view("admin/role/index",["roles"=>$roles]);
    }

    public function addView()
    {
        $permission_groups = $this->permissionGroupModel->getAll();
        return view("admin/role/add",["permission_groups"=>$permission_groups]);
    }

    public function add(Request $request)
    {
        if(!Gate::allows("create-role")){
            abort(401);
        }
        $validated = $request->validate([
            "name" => "bail|required|string|min:3|unique:roles",
            "permissions" => "nullable|array|exists:permissions,id"
        ]);
        $this->roleModel->saveRole($validated);
        return response()->redirect("admin/role/list")
                        ->with("success","Thêm vai trò thành công");
    }

    public function edit($id)
    {
        $role = $this->roleModel->getRole($id);
        if(!$role){
            return;
        }
        $permission_groups = $this->permissionGroupModel->getAll();
        return view("admin/role/edit",["role"=>$role,"permission_groups"=>$permission_groups]);
    }

    public function update(Request $request,$id)
    {
        if(!Gate::allows("update-role")){
            abort(401);
        }
        $validated = $request->validate([
            "name" => "bail|required|string|min:3|unique:roles,name,$id",
            "permissions" => "required|array|exists:permissions,id"
        ]);
        $role = Role::first(where:array("id"=>$id));
        if(!$role){
            return;
        }
        $role->updateRole($validated);
        return response()->redirect("admin/role/list")
                        ->with("success","Cập nhật vai trò thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-role")){
            abort(401);
        }
        $role = Role::first(where:array("id"=>$id));
        if(!$role){
            return;
        }
        $role->deleteRole();
        return response()->redirect("admin/role/list")
                        ->with("success","Xóa vai trò thành công");
    }
}