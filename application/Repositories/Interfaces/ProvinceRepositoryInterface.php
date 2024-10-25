<?php

namespace NhatHoa\App\Repositories\Interfaces;
use NhatHoa\App\Models\Province;

interface ProvinceRepositoryInterface
{
    public function getAll() : array;
    public function getById(int $id) : Province|null;
    public function create(array $data) : void;
    public function update(Province $province, array $data) : void;
    public function delete(Province $province) : void;
}