<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class Attribute extends Model
{
    public function getForCats()
    {
        return json_decode($this->for_categories);
    }

    public function getValues()
    {
        return $this->table("attribute_values")
                ->where("attribute_id",$this->id)
                ->get();
    }

    public function getValue($value_id)
    {
        return $this->table("attribute_values")
                ->where("id",$value_id)
                ->where("attribute_id",$this->id)
                ->first();
    }

    public function addValue($value)
    {
        $this->table("attribute_values")
            ->insert([
                "attribute_id" => $this->id,
                "value" => $value
            ]);
    }

    public function updateValue($value_id,$value)
    {
        $this->table("attribute_values")
            ->where("id",$value_id)
            ->where("attribute_id",$this->id)
            ->update([
                "value" => $value
            ]);
    }

    public function deleteValue($value_id)
    {
        $this->table("attribute_values")
            ->where("id",$value_id)
            ->where("attribute_id",$this->id)
            ->limit(1)
            ->delete();
    }
}