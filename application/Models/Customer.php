<?php

namespace NhatHoa\App\Models;
use NhatHoa\Framework\Abstract\Model;

class Customer extends Model
{
    public function getList($currentPage, $limit, $keyword)
    {
        if($keyword){
            $query = $this->where("name","like","%{$keyword}%")->orWhere("phone","like","%{$keyword}%");
        }else{
            $query = $this->orderBy("id","desc");
        }
        $number_of_customers = $query->count(false);
        $customers = $query->limit($limit)->offset(($currentPage - 1) * $limit)->get();
        return array($customers,$number_of_customers);
    }

    public function saveCustomer($validated)
    {
        $this->name = $validated["name"];
        $this->phone = $validated["phone"];
        $this->save();
    }

    public function updateCustomer($validated)
    {
        $this->saveCustomer($validated);
    }

    public function deleteCustomer()
    {
        $this->delete();
    }
}