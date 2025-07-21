<?php

namespace NhatHoa\App\Repositories;

use NhatHoa\App\Models\Attribute;
use NhatHoa\App\Repositories\BaseRepository;
use NhatHoa\App\Repositories\Interfaces\AttributeRepositoryInterface;

class AttributeRepository extends BaseRepository implements AttributeRepositoryInterface
{
    public function getAll() : array
    {
        return Attribute::all();
    }

    public function getById(string|int $id) : Attribute
    {
        return Attribute::first(where:array('id'=>$id));
    }

    public function store($validated) : void
    {
        $attribute = new Attribute();
        $attribute->name = $validated["name"];
        $attribute->for_categories = json_encode($validated['cats']);
        $attribute->save();
    }

    public function update(Attribute $attribute, $validated) : void
    {
        $attribute->name = $validated["name"];
        $attribute->for_categories = json_encode($validated['cats']);
        $attribute->save();
    }

    public function delete(Attribute $attribute) : void
    {
        $attribute->delete();
    }
}