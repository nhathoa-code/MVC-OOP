<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\PermissionGroup;
use NhatHoa\Framework\Validation\Rule;
use NhatHoa\Framework\Facades\Gate;

class PermissionGroupController extends Controller
{
    protected $permissionGroupModel;

    public function __construct(PermissionGroup $permission_group)
    {
        $this->permissionGroupModel = $permission_group;
    }

    public function index()
    {
        if(!Gate::allows("read-permission")){
            abort(401);
        }
        $permission_groups = PermissionGroup::all();
        return view("admin/permission_group/index",["permission_groups"=>$permission_groups]);
    }

    public function add(Request $request)
    {
        if(!Gate::allows("create-permission")){
            abort(401);
        }
        $validated = $request->validate([
            "name" => "bail|required|string|unique:permission_groups",
        ]);
        $this->permissionGroupModel->saveGroup($validated);
        return response()->back()->with("success","Thêm nhóm quyền thành công");
    }

    public function edit($id)
    {
        $permission_group = PermissionGroup::first(where:array("id"=>$id));
        if(!$permission_group){
            return;
        }
        return view("admin/permission_group/edit",["permission_group"=>$permission_group]);
    }

    public function update(Request $request,$id)
    {
        if(!Gate::allows("update-permission")){
            abort(401);
        }
        $validated = $request->validate([
            "name" => "bail|required|string|unique:permission_groups,name,$id",
        ]);
        $permission_group = PermissionGroup::first(where:array("id"=>$id));
        if(!$permission_group){
            return;
        }
        $permission_group->updateGroup($validated);
        return response()->redirect("admin/permission-group")->with("success","Sửa nhóm quyền thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-permission")){
            abort(401);
        }
        $permission_group = PermissionGroup::first(where:array("id"=>$id));
        if(!$permission_group){
            return;
        }
        $permission_group->deleteGroup();
        return response()->back()->with("success","Xóa nhóm quyền thành công");
    }

    public function permissions($group_id)
    {
        if(!Gate::allows("read-permission")){
            abort(401);
        }
        $permission_group = PermissionGroup::first(where:array("id"=>$group_id));
        if(!$permission_group){
            return;
        }
        return view("admin/permission_group/permissions",["permission_group"=>$permission_group]);
    }

    public function addPermission(Request $request,$group_id)
    {
        if(!Gate::allows("create-permission")){
            abort(401);
        }
        $validated = $request->validate([
            "name" => [
                "bail",
                "required",
                "string",
                Rule::unique("permissions")->where(function($query) use($group_id){
                    return $query->where("group_id",$group_id);
                })
            ]
        ]);
        $permission_group = PermissionGroup::first(where:array("id"=>$group_id));
        if(!$permission_group){
            return;
        }
        $permission_group->addPermission($validated['name']);
        return response()->back()->with("success","Thêm quyền thành công");
    }

    public function editPermission($group_id,$permission_id)
    {
        $permission_group = PermissionGroup::first(where:array("id"=>$group_id));
        if(!$permission_group){
            return;
        }
        $permission = $permission_group->getPermission($permission_id);
        return view("admin/permission_group/permission/edit",["permission_group"=>$permission_group,"permission"=>$permission]);
    }

    public function updatePermission(Request $request,$group_id,$permission_id)
    {
        if(!Gate::allows("update-permission")){
            abort(401);
        }
        $validated = $request->validate([
            "name" => [
                "bail",
                "required",
                "string",
                Rule::unique("permissions","name",$permission_id)->where(function($query) use($group_id){
                    return $query->where("group_id",$group_id);
                })
            ]
        ]);
        $permission_group = PermissionGroup::first(where:array("id"=>$group_id));
        if(!$permission_group){
            return;
        }
        $permission_group->updatePermission($permission_id,$validated['name']);
        return response()->redirect("admin/permission-group/{$group_id}/permissions")
                        ->with("success","Cập nhật quyền thành công");
    }

    public function deletePermission($group_id,$permission_id)
    {
        if(!Gate::allows("delete-permission")){
            abort(401);
        }
        $permission_group = PermissionGroup::first(where:array("id"=>$group_id));
        if(!$permission_group){
            return;
        }
        $permission_group->deletePermission($permission_id);
        return response()->back()->with("success","Xóa quyền thành công");
    }
}