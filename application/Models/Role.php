<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class Role extends Model
{
    public function getPermissions()
    {
        $permissions = $this->table("role_permissions as rp")
                        ->where("role_id",$this->id)
                        ->join("permissions as p","p.id","=","rp.permission_id")
                        ->join("permission_groups as pg","pg.id","=","p.group_id")
                        ->select(["p.id as p_id","p.name as action","pg.id","pg.name as resource"])
                        ->get();
        return $permissions;
    }

    public function formatPermissions($permissions)
    {
        $arr = array();
        foreach($permissions as $p){
            if(!isset($arr[$p->resource])){
                $arr[$p->resource] = array($p->action);
            }else{
                $arr[$p->resource][] = $p->action;
            }
        }
        return $arr;
    }
}