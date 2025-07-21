<?php

namespace NhatHoa\App\Repositories\Interfaces;

use NhatHoa\App\Models\Attribute;

interface AttributeRepositoryInterface
{
    public function getAll(): array;
    public function getById(int $id) : Attribute|null;
    public function store(array $data) : void;
    public function update(Attribute $attribute, array $data) : void;
    public function delete(Attribute $attribute) : void;
}