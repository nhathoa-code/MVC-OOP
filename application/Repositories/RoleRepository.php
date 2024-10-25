<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\App\Models\Role;
use NhatHoa\App\Repositories\Interfaces\RoleRepositoryInterface;
use NhatHoa\App\Repositories\BaseRepository;
use NhatHoa\Framework\Facades\DB;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function getAll() : array
    {
        $roles = Role::all();
        $roles = array_map(function($item){
            $item->permissions = $item->getPermissions();
            return $item;
        },$roles);
        return $roles;
    }

    public function getById($id) : Role|null
    {
        $role = Role::first(where:array("id"=>$id));
        if(!$role) return null;
        $role->permissions = array_map(function($item){
            return $item->p_id;
        },$role->getPermissions());
        return $role;
    }

    public function create($validated) : void
    {
        $role = new Role();
        $role->name = $validated['name'];
        $role_id = $role->save();
        foreach($validated["permissions"] as $permis){
            DB::table("role_permissions")
                ->insert([
                    "role_id" => $role_id,
                    "permission_id" => $permis
                ]);
        }
    }

    public function update(Role $role,$validated) : void
    {
        $role->name = $validated['name'];
        $permissions_from_db = DB::table("role_permissions")
                                    ->where("role_id",$role->id)
                                    ->getArray("permission_id");
        $permissions_from_client = $validated["permissions"];
        $full_permissions_diff = array_merge(array_diff($permissions_from_db,$permissions_from_client),array_diff($permissions_from_client,$permissions_from_db));
        foreach($full_permissions_diff as $item){
            if(in_array($item,$permissions_from_db)){
                DB::table("role_permissions")
                    ->where("role_id", $role->id)
                    ->where("permission_id",$item)
                    ->delete();
            }else{
                DB::table("role_permissions")
                    ->insert([
                        "role_id" => $role->id,
                        "permission_id" => $item
                ]   );
            }
        }
        $role->save();
    }

    public function delete(Role $role) : void
    {
        $role->delete();
    }
}