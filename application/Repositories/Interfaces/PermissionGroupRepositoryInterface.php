<?php

namespace NhatHoa\App\Repositories\Interfaces;
use NhatHoa\App\Models\PermissionGroup;

interface PermissionGroupRepositoryInterface
{
    public function getAll() : array; 
    public function getById(int $id) : PermissionGroup|null;
    public function create(array $data) : void;
    public function update(PermissionGroup $permissionGroup, array $data) : void;
    public function delete(PermissionGroup $permissionGroup) : void;
} 