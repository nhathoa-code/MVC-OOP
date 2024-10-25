<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class Province extends Model
{
    public function getDistricts()
    {
        return $this->table("province_districts")
                ->where("province_id",$this->id)
                ->get();
    }

    public function getDistrict($district_id)
    {
        return $this->table("province_districts")
                ->where("id",$district_id)
                ->where("province_id",$this->id)
                ->first();
    }

    public function addDistrict($name)
    {
        $this->table("province_districts")
            ->insert([
                "province_id" => $this->id,
                "name" => $name
            ]);
    }

    public function updateDistrict($district_id,$district_name)
    {
        $this->table("province_districts")
            ->where("id",$district_id)
            ->where("province_id",$this->id)
            ->update([
                "name" => $district_name
            ]);
    }

    public function deleteDistrict($district_id)
    {
        $this->table("province_districts")
            ->where("id",$district_id)
            ->where("province_id",$this->id)
            ->limit(1)
            ->delete();
    }
}