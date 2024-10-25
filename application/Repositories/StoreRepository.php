<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\App\Models\Store;
use NhatHoa\App\Repositories\Interfaces\StoreRepositoryInterface;
use NhatHoa\App\Repositories\BaseRepository;

class StoreRepository extends BaseRepository implements StoreRepositoryInterface
{
    public function getAll() : array
    {
        $table = Store::getTable();
        return Store::query()->join("provinces as p","p.id","=",$table . ".province_id")
                ->join("province_districts as pd","pd.id","=",$table . ".district_id")
                ->select(["{$table}.*","p.name as province","pd.name as district"])
                ->get();
    }

    public function getById($id) : Store|null
    {
        $store = Store::first(where:array("id"=>$id));
        return $store;
    }

    public function create($validated) : void
    {
        $store = new Store();
        $store->name = $validated["name"];
        $store->address = $validated["address"];
        $store->coordinates = $validated["coordinates"];
        $store->province_id = $validated["province_id"];
        $store->district_id = $validated["district_id"];
        $store->save();
    }

    public function update(Store $store,$validated) : void
    {
        $store->name = $validated["name"];
        $store->address = $validated["address"];
        $store->coordinates = $validated["coordinates"];
        $store->province_id = $validated["province_id"];
        $store->district_id = $validated["district_id"];
        $store->save();
    }

    public function delete(Store $store) : void
    {
        $store->delete();
    }
}