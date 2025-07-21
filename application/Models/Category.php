<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class Category extends Model
{
    protected $_table = 'categories';

    public function hasChildren()
    {
        $children = $this->exists(where:array("parent_id" => $this->id));
        if($children) return true;
        return false;
    }

    public function getChildren()
    {
        return $this->all(where:array("parent_id" => $this->id));
    }

    public function hasParent()
    {
        $parent = $this->exists(
            where:array("id"=>$this->id),
            whereNotNull:array("parent_id")
        );
        if($parent) return true;
        return false;
    }

    public function getParent()
    {
        return $this->first(where:array("id"=>$this->parent_id));
    }

    public function countProducts()
    {
        $count = $this->count(table:"product_categories", where:array("cat_id" => $this->id));
        return $count;
    }

    public function getSiblings()
    {
        return $this->all(where:array(array("id"=>$this->id,"operator"=>"!=")));
    }

    public function getAttributes()
    {
        $attributes = Attribute::query()
            ->whereJsonContains('for_categories', $this->id)
            ->get();
        foreach($attributes as $attr){
            $attr->values = $attr->getValues();
        }
        return $attributes;
    }
}

