<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\App\Models\Province;
use NhatHoa\App\Repositories\Interfaces\ProvinceRepositoryInterface;
use NhatHoa\App\Repositories\BaseRepository;

class ProvinceRepository extends BaseRepository implements ProvinceRepositoryInterface
{
    public function getAll() : array
    {
        return Province::all();
    }

    public function getById($id) : Province|null
    {
        return Province::first(where:array("id"=>$id));
    }

    public function create($validated) : void
    {
        $province = new Province();
        $province->name = $validated["name"];
        $province->save();
    }

    public function update(Province $province, $validated) : void
    {
        $province->name = $validated["name"];
        $province->save();
    }

    public function delete(Province $province) : void
    {
        $province->delete();
    }
}