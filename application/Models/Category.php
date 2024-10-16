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

    public function countProducts()
    {
        $count = $this->count(table:"product_categories", where:array("cat_id" => $this->id));
        return $count;
    }

    public function getSiblings()
    {
        return $this->all(where:array(array("id"=>$this->id,"operator"=>"!=")));
    }
}

