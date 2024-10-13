<?php

namespace NhatHoa\App\Repositories\Interfaces;
use NhatHoa\App\Models\Store;

interface StoreRepositoryInterface
{
    public function getAll() : array;
    public function getById(int $id) : Store|null;
    public function create(array $data) : void;
    public function update(Store $store, array $data) : void;
    public function delete(Store $store) : void;
} 