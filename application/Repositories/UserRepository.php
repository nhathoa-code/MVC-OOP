<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\App\Models\User;
use NhatHoa\App\Repositories\Interfaces\UserRepositoryInterface;
use NhatHoa\App\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getAll($currentPage, $limit, $role, $keyword, $unverified) : array
    {
        $query = User::query()->leftJoin("roles","roles.id","=","users.role_id");
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

    public function getById($id) : User|null
    {
        return User::query()->leftJoin("roles","roles.id","=","users.role_id")
                ->select(["users.*","roles.name as role"])
                ->where("users.id",$id)->first();
    }

    public function create($validated) : void
    {
        $user = new User();
        $user->email = $validated["email"];
        $user->name = $validated["name"];
        $user->login_key = $validated["login_key"];
        $user->password = password_hash($validated["password"],PASSWORD_DEFAULT);
        if(isset($validated["role"])){
            $user->role_id = $validated["role"];
        }
        $user->email_verified_at = date("Y-m-d H:i:s", time());
        $user_id = $user->save();
        $user->id = $user_id;
        if(!isset($validated["role"])){
            $user->setUpMetuser();
        }
    }

    public function update(User $user,$validated) : void
    {
        $user->email = $validated["email"];
        $user->name = $validated["name"];
        $user->login_key = $validated["login_key"];
        if(isset($validated["password"])){
            $user->password = password_hash($validated["password"],PASSWORD_DEFAULT);
        }
        $user->role_id = $validated["role"] ?? null;
        $user->email_verified_at = date("Y-m-d H:i:s", time());
        $user->save();
    }

    public function delete(User $user) : bool
    {
        if($user->role === "admin" && $user->email_verified_at === null){
            return false;
        }
        $user->delete();
        return true;
    }

    public function register($validated) : string
    {
        $user = new User();
        $user->email = $validated["email"];
        $user->name = $validated["name"];
        $user->login_key = $validated["email"];
        $user->password = password_hash($validated["password"],PASSWORD_DEFAULT);
        $id = $user->save();
        $user->id = $id;
        $verify_token = generateToken();
        $user->insertMeta("verify_token",$verify_token);
        return $verify_token;
    }
}