<?php

namespace NhatHoa\App\Repositories\Interfaces;
use NhatHoa\App\Models\Category;
use NhatHoa\Framework\Core\Request;

interface CategoryRepositoryInterface
{
    public function getAll() : array;
    public function getById(int $id) : Category|null;
    public function create(array $data,Request $request) : void;
    public function update(Category $category, array $data,Request $request) : void;
    public function delete(Category $category) : void;
    public function fetchAll(array $categories = null) : array;
}