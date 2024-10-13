<?php

namespace NhatHoa\App\Services;
use NhatHoa\Framework\Abstract\Service;
use NhatHoa\App\Models\User;
use NhatHoa\Framework\Facades\DB;
use NhatHoa\App\Repositories\Interfaces\RoleRepositoryInterface;

class UserService extends Service
{
    public function countUser($role)
    {
        $query = User::query()->leftJoin("roles","roles.id","=","users.role_id");
        if($role == "user"){
            $query->whereNull("role_id");
        }else{
            $query->where("roles.name",$role);
        }
        return $query->count();
    }

    public function login($login_key,$password)
    {
        $user = User::first(
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

    public function adminLogin($login_key,$password,RoleRepositoryInterface $roleRepository)
    {
        $admin = User::query()->join("roles","roles.id","=","users.role_id")
                    ->where("login_key",$login_key)
                    ->whereNotNull("role_id")
                    ->select(["users.*","roles.name as role"])
                    ->first();
        if($admin){
            $admin_password = $admin->password;
            if(password_verify($password,$admin_password)){
                $role = $roleRepository->getById($admin->role_id);
                $admin->permissions = $role->formatPermissions($role->getPermissions());
                return $admin;
            }else return false;
        }else return false;
    }

    public function plusPoint(User $user,int $point)
    {
        if($point > 0){
            DB::table("user_meta")
                ->where("user_id",$user->id)
                ->where("meta_key","point")
                ->limit(1)
                ->update([
                    "meta_value" => (int) $user->getMeta("point") + $point
                ]);
        }
    }

    public function minusPoint(User $user,int $point)
    {
        if($point > 0){
            DB::table("user_meta")
                ->where("user_id",$user->id)
                ->where("meta_key","point")
                ->limit(1)
                ->update([
                    "meta_value" => (int) $user->getMeta("point") - $point
                ]);   
        }
    }

    public function insertPointHistory(User $user, $point, $action = 1, $order_id, $order_total)
    {
        DB::table("point_history")
                ->insert([
                    "user_id" => $user->id,
                    "order_id" => $order_id,
                    "order_total" => $order_total,
                    "point" => $point,
                    "action" => $action
                ]);
    }

    public function deletePointHistory(User $user, $order_id, $action)
    {
        DB::table("point_history")
            ->where("user_id",$user->id)
            ->where("order_id",$order_id)
            ->where("action", $action)
            ->limit(1)
            ->delete();
    }

     public function updateRank(User $user,$total_spend)
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
        DB::table("user_meta")
            ->where("user_id",$user->id)
            ->where("meta_key","rank")
            ->limit(1)
            ->update([
                "meta_value" => $rank
            ]);
    }

    public function updateTotalSpend(User $user, $total_pay, $sign)
    {
        $current_spend = $user->getMeta("total_spend");
        $value = $current_spend;
        switch($sign){
            case "+":
                $value += $total_pay;
                break;
            case "-":
                $value -= $total_pay;    
        }
        DB::table("user_meta")
            ->where("user_id",$user->id)
            ->where("meta_key","total_spend")
            ->update([
                "meta_value" => $value
            ]);
        return $value;
    }
}