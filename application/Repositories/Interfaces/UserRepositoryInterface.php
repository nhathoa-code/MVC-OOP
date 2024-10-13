<?php

namespace NhatHoa\App\Repositories\Interfaces;
use NhatHoa\App\Models\User;

interface UserRepositoryInterface
{
    public function getAll(int $currentPage,int $limit,string $role,string $keyword,bool|null $unverified) : array;
    public function getById(int $id) : User|null;
    public function create(array $data) : void;
    public function update(User $user, array $data) : void;
    public function delete(User $user) : null|bool;
    public function register(array $data) : string;
}