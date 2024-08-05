<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class PermissionGroup extends Model
{
    protected $_table = "permission_groups";
    
    public function getAll()
    {
        $groups = $this->all();
        $groups = array_map(function($item){
            $item->permissions = $item->getPermissions();
            return $item;
        },$groups);
        return $groups;
    }

    public function saveGroup($validated)
    {
        $this->name = $validated['name'];
        $this->save();
    }

    public function updateGroup($validated)
    {
        $this->saveGroup($validated);
    }

    public function deleteGroup()
    {
        $this->delete();
    }

    public function addPermission($name)
    {
        $this->table("permissions")
            ->insert([
                "name" => $name,
                "group_id" => $this->id
            ]);
    }

    public function getPermissions()
    {
        return $this->table("permissions")
                    ->where("group_id",$this->id)
                    ->get();
    }

    public function getPermission($permission_id)
    {
        return $this->table("permissions")
                    ->where("group_id",$this->id)
                    ->where("id",$permission_id)
                    ->first();
    }

    public function updatePermission($permission_id,$name)
    {
        $this->table("permissions")
            ->where("group_id",$this->id)
            ->where("id",$permission_id)
            ->update([
                "name" => $name
            ]);
    }

    public function deletePermission($permission_id)
    {
        $this->table("permissions")
            ->where("group_id",$this->id)
            ->where("id",$permission_id)
            ->delete();
    }
}