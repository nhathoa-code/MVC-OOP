<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class User extends Model
{
    protected $_hidden = ["remember_token"];

    public function getList($currentPage, $limit, $role, $keyword, $unverified)
    {
        $query = $this->leftJoin("roles","roles.id","=","users.role_id");
        if($role){
            if($role == "user"){
                $query->whereNull("role_id");
            }else{
                $query->where("roles.name",$role);
            }
        }elseif($unverified){
            $query->whereNull("email_verified_at")->whereNull("role_id");
        }
        if($keyword){
            $query->where(function($query) use($keyword){
                $query->where("users.name","like","%$keyword%")->orWhere("users.email","like","%$keyword%");
            });
        }
        $total_users = $query->count(false);
        $users = $query->select(["users.*","roles.name as role"])
                    ->orderBy("users.id","desc")
                    ->limit($limit)->offset(($currentPage - 1) * $limit)->get();
        return [$users,$total_users];
    }

    public function countUser($role)
    {
        $query = $this->leftJoin("roles","roles.id","=","users.role_id");
        if($role == "user"){
            $query->whereNull("role_id");
        }else{
            $query->where("roles.name",$role);
        }
        return $query->count();
    }

    public function getProfile()
    {
        $this->meta = $this->getMetas();
        if(!empty($this->meta)){
            $this->meta = array_map(function($item){
                return [$item->meta_key => $item->meta_value];
            },$this->meta);
            $this->meta = array_merge(...$this->meta);
        }
        $this->profile = $this->getMeta("profile");
        if($this->profile){
            $this->profile = unserialize($this->profile);
        }
        $this->toship_orders = $this->countOrders("toship");
        $this->shipping_orders = $this->countOrders("shipping");
        $this->completed_orders = $this->countOrders("completed");
        $this->cancelled_orders = $this->countOrders("cancelled");
    }

    public function addAddress($validated)
    {
        $data = array(
            "name" => $validated["name"],
            "phone" => $validated["phone"],
            "province" => $validated["province"],
            "district" => $validated["district"],
            "ward" => $validated["ward"],
            "address" => $validated["address"],
            "province_id" => $validated["province_id"],
            "district_id" => $validated["district_id"],
            "ward_code" => $validated["ward_code"]
        );
        if(isset($validated["default"])){
            $addresses = $this->getMeta("address",true);
            foreach($addresses as $item){
                $add = unserialize($item->meta_value);
                if(array_key_exists("default",$add)){
                    unset($add['default']);
                    $this->updateMeta("address",serialize($add),$item->id);
                }
            }
            $data['default'] = true;
        }
        $this->insertMeta("address",serialize($data));
    }

    public function updateAddress($validated,$address_id)
    {
        $data = array(
            "name" => $validated["name"],
            "phone" => $validated["phone"],
            "province" => $validated["province"],
            "district" => $validated["district"],
            "ward" => $validated["ward"],
            "address" => $validated["address"],
            "province_id" => $validated["province_id"],
            "district_id" => $validated["district_id"],
            "ward_code" => $validated["ward_code"]
        );
        if(isset($validated["default"])){
            $addresses = $this->getMeta("address",true);
            foreach($addresses as $item){
                $add = unserialize($item->meta_value);
                if(array_key_exists("default",$add)){
                    unset($add['default']);
                    $this->updateMeta("address",serialize($add),$item->id);
                }
            }
            $data['default'] = true;
        }
        $this->updateMeta("address",serialize($data),$address_id);
    }

    public function deleteAddress($address_id)
    {
        $this->deleteMeta("address",$address_id);
    }

    public function countOrders($status = "")
    {
        return $this->count(
            table:"orders",
            where:array(
                "user_id"=>$this->id,
                "status"=>$status
            )
        );
    }

    public function getOrders($status = "")
    {
        return $this->table("orders")
            ->where("user_id",$this->id)
            ->where("status",$status)
            ->orderBy("created_at","desc")
            ->get();
    }

    public function getOrder($order_id)
    {
        return new Order($this->table("orders")
                ->where("id",$order_id)
                ->where("user_id",$this->id)
                ->first());
    }

    public function plusPoint(int $point)
    {
        if($point > 0){
            $this->table("user_meta")
                ->where("user_id",$this->id)
                ->where("meta_key","point")
                ->limit(1)
                ->update([
                    "meta_value" => (int) $this->getMeta("point") + $point
                ]);
        }
    }

    public function minusPoint(int $point)
    {
        if($point > 0){
            $this->table("user_meta")
                ->where("user_id",$this->id)
                ->where("meta_key","point")
                ->limit(1)
                ->update([
                    "meta_value" => (int) $this->getMeta("point") - $point
                ]);   
        }
      
    }

    public function getPointHistory()
    {
        return $this->table("point_history")
                ->where("user_id",$this->id)
                ->get();
    }

    public function insertPointHistory($point, $action = 1, $order_id, $order_total)
    {
        $this->table("point_history")
                ->insert([
                    "user_id" => $this->id,
                    "order_id" => $order_id,
                    "order_total" => $order_total,
                    "point" => $point,
                    "action" => $action
                ]);
    }

    public function deletePointHistory($order_id, $action)
    {
        $this->table("point_history")
            ->where("user_id",$this->id)
            ->where("order_id",$order_id)
            ->where("action", $action)
            ->limit(1)
            ->delete();
    }

    public function updateRank($total_spend)
    {
        switch (true) {
            case $total_spend < 10000000:
                $rank = "member";
                break;
            case $total_spend >= 10000000 && $total_spend < 20000000:
                $rank = "silver";
                break;
            case $total_spend >= 20000000 && $total_spend < 35000000:
                $rank = "gold";
                break;
            case $total_spend >= 35000000 && $total_spend < 50000000:
                $rank = "platinum";
                break;
            case $total_spend > 50000000:
                $rank = "diamond";
                break;
            default:
                $rank = "member";    
        }
        $this->table("user_meta")
            ->where("user_id",$this->id)
            ->where("meta_key","rank")
            ->limit(1)
            ->update([
                "meta_value" => $rank
            ]);
    }

    public function updateTotalSpend($total_pay, $sign)
    {
        $current_spend = $this->getMeta("total_spend");
        $value = $current_spend;
        switch($sign){
            case "+":
                $value += $total_pay;
                break;
            case "-":
                $value -= $total_pay;    
        }
        $this->table("user_meta")
            ->where("user_id",$this->id)
            ->where("meta_key","total_spend")
            ->update([
                "meta_value" => $value
            ]);
        return $value;
    }

    public function getMeta($meta_key,$multiple = false,$meta_id = null,$full = false)
    {
        $query = $this->table("user_meta")
                ->where("user_id",$this->id)
                ->where("meta_key", $meta_key);
        if($meta_id){
            $query->where("id",$meta_id);
        }
        if(!$multiple){
            if(!$full){
                return $query->first("meta_value");
            }else{
                return $query->first();
            }
        }else{
            return $query->get();
        }
    }

    public function getMetas()
    {
        return $this->table("user_meta")
                    ->where("user_id",$this->id)
                    ->get();
    }

    public function addUser($validated)
    {
        $this->email = $validated["email"];
        $this->name = $validated["name"];
        $this->login_key = $validated["login_key"];
        $this->password = password_hash($validated["password"],PASSWORD_DEFAULT);
        if(isset($validated["role"])){
            $this->role_id = $validated["role"];
        }
        $this->email_verified_at = date("Y-m-d H:i:s", time());
        $user_id = $this->save();
        $this->id = $user_id;
        if(!isset($validated["role"])){
            $this->setUpMeta();
        }
    }

    public function getUser($id)
    {
        return $this->leftJoin("roles","roles.id","=","users.role_id")
                ->select(["users.*","roles.name as role"])
                ->where("users.id",$id)->first();
    }

    public function updateUser($validated)
    {
        $this->email = $validated["email"];
        $this->name = $validated["name"];
        $this->login_key = $validated["login_key"];
        if(isset($validated["password"])){
            $this->password = password_hash($validated["password"],PASSWORD_DEFAULT);
        }
        $this->role_id = $validated["role"] ?? null;
        $this->email_verified_at = date("Y-m-d H:i:s", time());
        $this->save();
    }

    public function deleteUser($id)
    {
        $user = $this->first(where:array("id"=>$id));
        if(!$user){
            return false;
        }
        if($user->role === "admin" && $user->email_verified_at === null){
            return false;
        }
        $user->delete();
    }

    public function setUpMeta()
    {
        $this->table("user_meta")->insert([
            "user_id" => $this->id,
            "meta_key" => "point",
            "meta_value" => 0
        ]);
        $this->table("user_meta")->insert([
            "user_id" => $this->id,
            "meta_key" => "total_spend",
            "meta_value" => 0
        ]);
        $this->table("user_meta")->insert([
            "user_id" => $this->id,
            "meta_key" => "rank",
            "meta_value" => "member"
        ]);
    }

    public function updateMeta($meta_key,$meta_value,$meta_id = null)
    {
        $query = $this->table("user_meta")
            ->where("user_id",$this->id)
            ->where("meta_key",$meta_key);
        if($meta_id){
            $query->where("id",$meta_id);
        }
        $query->limit(1)
            ->update([
                "meta_value" => $meta_value
            ]);
    }

    public function insertMeta($meta_key,$meta_value)
    {
        $this->table("user_meta")
            ->insert([
                "user_id" => $this->id,
                "meta_key" => $meta_key,
                "meta_value" => $meta_value
            ]);
    }

    public function deleteMeta($meta_key,$meta_id = null)
    {
        $query = $this->table("user_meta")
            ->where("user_id",$this->id)
            ->where("meta_key",$meta_key);
        if($meta_id){
            $query->where("id",$meta_id);
        }
        $query->limit(1)
            ->delete();
    }

    public function updateProfile($validated)
    {
        $data = array(
            "name" => $validated["name"],
            "phone" => $validated["phone"],
            "email" => $validated["email"],
            "birth_day" => $validated["birth_day"],
            "province" => $validated["province"],
            "province_id" => $validated["province_id"],
            "district" => $validated["district"],
            "district_id" => $validated["district_id"],
            "ward" => $validated["ward"],
            "ward_code" => $validated["ward_code"],
            "gender" => $validated["gender"]
        );
        $this->table("user_meta")
            ->updateOrCreate(
                [
                    "user_id" => $this->id,
                    "meta_key" => "profile"
                ],
                [
                    "meta_value" => serialize($data)
                ]
            );
    }

    public function hasWishList($product_id)
    {
        $record = $this->table("wish_list")
                    ->where("user_id",$this->id)
                    ->where("p_id",$product_id)
                    ->first();
        if($record){
            return true;
        }
        return false;
    }

    public function countWishList()
    {
        return $this->table("wish_list")
                    ->where("user_id",$this->id)
                    ->count();
    }

    public function getWishList()
    {
        $ids = $this->table("wish_list as wl")
                    ->join("products as p","p.id","=","wl.p_id")
                    ->where("user_id",$this->id)
                    ->getArray("p_id");
        return Product::all(whereIn:array("id"=>$ids));
    }

    public function login($login_key,$password)
    {
        $user = $this->first(
            where:array("email" => $login_key),
        );
        if($user){
            if(password_verify($password,$user->password)){
                if($user->email_verified_at){
                    return array("status"=>true,"user"=>$user);
                }else{
                    return array("status"=>false,"message"=>"Tài khoản chưa xác thực email");
                }
            }else{  
                return array("status"=>false,"message"=>"Email hoặc mật khẩu không đúng!");
            }
        }else{
            return array("status"=>false,"message"=>"Email hoặc mật khẩu không đúng!");
        }
    }

    public function adminLogin($login_key,$password)
    {
        $admin = $this->join("roles","roles.id","=","users.role_id")
                    ->where("login_key",$login_key)
                    ->whereNotNull("role_id")
                    ->select(["users.*","roles.name as role"])
                    ->first();
        if($admin){
            $admin_password = $admin->password;
            if(password_verify($password,$admin_password)){
                $admin->permissions = $admin->formatPermissions($admin->getPermissions($admin->role_id));
                return $admin;
            }else{  
                return false;
            }
        }else{
            return false;
        }
    }

    public function register($validated)
    {
        $this->email = $validated["email"];
        $this->name = $validated["name"];
        $this->login_key = $validated["email"];
        $this->password = password_hash($validated["password"],PASSWORD_DEFAULT);
        $id = $this->save();
        $this->id = $id;
        $verify_token = generateToken();
        $this->insertMeta("verify_token",$verify_token);
        return $verify_token;
    }

    public function getPermissions($role_id)
    {
        $permissions = $this->table("role_permissions as rp")
                        ->where("role_id",$role_id)
                        ->join("permissions as p","p.id","=","rp.permission_id")
                        ->join("permission_groups as pg","pg.id","=","p.group_id")
                        ->select(["p.name as action","pg.id","pg.name as resource"])
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

    public function can(string $action,string $resource)
    {
        $permissions = $this->permissions ?? [];
        if(isset($permissions[$resource])){
            if(in_array($action,$permissions[$resource])){
                return true;
            }
            return false;
        }else{
            return false;
        }
    }
}