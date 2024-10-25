<?php

namespace NhatHoa\App\Repositories\Interfaces;
use NhatHoa\App\Models\Role;

interface RoleRepositoryInterface
{
    public function getAll() : array;
    public function getById(int $id) : Role|null; 
    public function create(array $data) : void;
    public function update(Role $role, array $data) : void;
    public function delete(Role $role) : void;
} 