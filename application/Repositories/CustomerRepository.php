<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\App\Models\Customer;
use NhatHoa\App\Repositories\Interfaces\CustomerRepositoryInterface;
use NhatHoa\App\Repositories\BaseRepository;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    public function getAll($currentPage, $limit, $keyword) : array
    {
        if($keyword){
            $query = Customer::where("name","like","%{$keyword}%")->orWhere("phone","like","%{$keyword}%");
        }else{
            $query = Customer::orderBy("id","desc");
        }
        $number_of_customers = $query->count(false);
        $customers = $query->limit($limit)->offset(($currentPage - 1) * $limit)->get();
        return array($customers,$number_of_customers);
    }

    public function getById($id) : Customer|null
    {
        return Customer::first(where:array("id"=>$id));
    }

    public function getByPhoneNumber(string $phone_number) : Customer|null
    {
        return Customer::first(where:array("phone"=>$phone_number));
    }

    public function create($validated) : void
    {
        $customer = new Customer();
        $customer->name = $validated["name"];
        $customer->phone = $validated["phone"];
        $customer->save();
    }

    public function update(Customer $customer,$validated) : void
    {
        $customer->name = $validated["name"];
        $customer->phone = $validated["phone"];
        $customer->save();
    }

    public function delete(Customer $customer) : void
    {
        $customer->delete();
    }
}