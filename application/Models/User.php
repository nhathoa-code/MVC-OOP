<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class User extends Model
{
    protected $_hidden = ["remember_token"];

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
        $where = array("user_id"=>$this->id);
        if(!empty($status)){
            $where["status"] = $status;
        }
        return $this->count(
            table:"orders",
            where:$where
        );
    }

    public function getOrders($status = "")
    {
        $query = $this->table("orders")
                ->where("user_id",$this->id);
        if(!empty($status)){
            $query->where("status",$status);
        } 
        return $query->orderBy("created_at","desc")->get();
    }

    public function getOrder($order_id)
    {
        return new Order($this->table("orders")
                ->where("id",$order_id)
                ->where("user_id",$this->id)
                ->first());
    }

    public function getPointHistory()
    {
        return $this->table("point_history")
                ->where("user_id",$this->id)
                ->get();
    }

    public function getMeta($meta_key, $multiple = false, $meta_id = null, $full = false)
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
        if($record) return true;
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