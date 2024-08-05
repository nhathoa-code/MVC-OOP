<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class Role extends Model
{
    public function getAll()
    {
        $roles = $this->all();
        $roles = array_map(function($item){
            $item->permissions = $item->getPermissions();
            return $item;
        },$roles);
        return $roles;
    } 

    public function getRole($id)
    {
        $role = $this->first(where:array("id"=>$id));
        $role->permissions = array_map(function($item){
            return $item->permission_id;
        },$role->getPermissions());
        return $role;
    }

    public function saveRole($validated)
    {
        $this->name = $validated['name'];
        $role_id = $this->save();
        foreach($validated["permissions"] as $permis){
            $this->table("role_permissions")
                ->insert([
                    "role_id" => $role_id,
                    "permission_id" => $permis
                ]);
        }
    }

    public function updateRole($validated)
    {
        $this->name = $validated['name'];
        $permissions_from_db = $this->table("role_permissions")
                                    ->where("role_id",$this->id)
                                    ->getArray("permission_id");
        $permissions_from_client = $validated["permissions"];
        $full_permissions_diff = array_merge(array_diff($permissions_from_db,$permissions_from_client),array_diff($permissions_from_client,$permissions_from_db));
        foreach($full_permissions_diff as $item){
            if(in_array($item,$permissions_from_db)){
                $this->table("role_permissions")
                    ->where("role_id", $this->id)
                    ->where("permission_id",$item)
                    ->delete();
            }else{
                $this->table("role_permissions")
                    ->insert([
                        "role_id" => $this->id,
                        "permission_id" => $item
                ]   );
            }
        }
        $this->save();
    }

    public function getPermissions()
    {
        return $this->table("role_permissions as rp")
                ->where("rp.role_id",$this->id)
                ->join("permissions as p","p.id","=","rp.permission_id")
                ->get();
    }

    public function deleteRole()
    {
        $this->delete();
    }

}