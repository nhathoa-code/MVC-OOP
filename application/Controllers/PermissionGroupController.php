<?php

namespace NhatHoa\App\Controllers;
use NhatHoa\Framework\Core\Request;
use NhatHoa\Framework\Abstract\Controller;
use NhatHoa\App\Models\PermissionGroup;
use NhatHoa\App\Repositories\Interfaces\PermissionGroupRepositoryInterface;
use NhatHoa\Framework\Validation\Rule;
use NhatHoa\Framework\Facades\Gate;

class PermissionGroupController extends Controller
{
    protected $permissionGroupRepository;

    public function __construct(PermissionGroupRepositoryInterface $permissionGroupRepository)
    {
        $this->permissionGroupRepository = $permissionGroupRepository;
    }

    public function index()
    {
        if(!Gate::allows("read-permission")) abort(401);
        $permissionGroups = PermissionGroup::all();
        return view("admin/permission_group/index",["permission_groups"=>$permissionGroups]);
    }

    public function add(Request $request)
    {
        if(!Gate::allows("create-permission")) abort(401);
        $validated = $request->validate([
            "name" => "bail|required|string|unique:permission_groups",
        ]);
        $this->permissionGroupRepository->create($validated);
        return response()->back()->with("success","Thêm nhóm quyền thành công");
    }

    public function edit($id)
    {
        $permissionGroup = $this->permissionGroupRepository->getById($id);
        if(!$permissionGroup) return;
        return view("admin/permission_group/edit",["permission_group"=>$permissionGroup]);
    }

    public function update(Request $request,$id)
    {
        if(!Gate::allows("update-permission")) abort(401);
        $validated = $request->validate([
            "name" => "bail|required|string|unique:permission_groups,name,$id",
        ]);
        $permissionGroup = $this->permissionGroupRepository->getById($id);
        if(!$permissionGroup) return;
        $this->permissionGroupRepository->update($permissionGroup,$validated);
        return response()->redirect("admin/permission-group")->with("success","Sửa nhóm quyền thành công");
    }

    public function delete($id)
    {
        if(!Gate::allows("delete-permission")) abort(401);
        $permissionGroup = $this->permissionGroupRepository->getById($id);
        if(!$permissionGroup) return;
        $this->permissionGroupRepository->delete($permissionGroup);
        return response()->back()->with("success","Xóa nhóm quyền thành công");
    }

    public function permissions($group_id)
    {
        if(!Gate::allows("read-permission")) abort(401);
        $permissionGroup = $this->permissionGroupRepository->getById($group_id);
        if(!$permissionGroup) return;
        return view("admin/permission_group/permissions",["permission_group"=>$permissionGroup]);
    }

    public function addPermission(Request $request,$group_id)
    {
        if(!Gate::allows("create-permission")) abort(401);
        $validated = $request->validate([
            "name" => ["bail","required","string",
                Rule::unique("permissions")->where(function($query) use($group_id){
                    return $query->where("group_id",$group_id);
                })
            ]
        ]);
        $permissionGroup = $this->permissionGroupRepository->getById($group_id);
        if(!$permissionGroup) return;
        $permissionGroup->addPermission($validated['name']);
        return response()->back()->with("success","Thêm quyền thành công");
    }

    public function editPermission($group_id,$permission_id)
    {
        $permissionGroup = $this->permissionGroupRepository->getById($group_id);
        if(!$permissionGroup) return;
        $permission = $permissionGroup->getPermission($permission_id);
        return view("admin/permission_group/permission/edit",["permission_group"=>$permissionGroup,"permission"=>$permission]);
    }

    public function updatePermission(Request $request,$group_id,$permission_id)
    {
        if(!Gate::allows("update-permission")) abort(401);
        $validated = $request->validate([
            "name" => ["bail","required","string",
                Rule::unique("permissions","name",$permission_id)->where(function($query) use($group_id){
                    return $query->where("group_id",$group_id);
                })
            ]
        ]);
        $permissionGroup = $this->permissionGroupRepository->getById($group_id);
        if(!$permissionGroup) return;
        $permissionGroup->updatePermission($permission_id,$validated['name']);
        return response()->redirect("admin/permission-group/{$group_id}/permissions")
                        ->with("success","Cập nhật quyền thành công");
    }

    public function deletePermission($group_id,$permission_id)
    {
        if(!Gate::allows("delete-permission")) abort(401);
        $permissionGroup = $this->permissionGroupRepository->getById($group_id);
        if(!$permissionGroup) return;
        $permissionGroup->deletePermission($permission_id);
        return response()->back()->with("success","Xóa quyền thành công");
    }
}