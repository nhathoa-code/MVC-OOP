<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\App\Models\PermissionGroup;
use NhatHoa\App\Repositories\Interfaces\PermissionGroupRepositoryInterface;
use NhatHoa\App\Repositories\BaseRepository;

class PermissionGroupRepository extends BaseRepository implements PermissionGroupRepositoryInterface
{
    public function getAll() : array
    {
        $groups = PermissionGroup::all();
        $groups = array_map(function($item){
            $item->permissions = $item->getPermissions();
            return $item;
        },$groups);
        return $groups;
    }

    public function getById(int $id) : PermissionGroup|null
    {
        return PermissionGroup::first(where:array("id"=>$id));
    }

    public function create($validated) : void
    {
        $permissionGroup = new PermissionGroup();
        $permissionGroup->name = $validated['name'];
        $permissionGroup->save();
    }

    public function update(PermissionGroup $permissionGroup,$validated) : void
    {
        $permissionGroup->name = $validated['name'];
        $permissionGroup->save();
    }

    public function delete(PermissionGroup $permissionGroup) : void
    {
        $permissionGroup->delete();
    }
}