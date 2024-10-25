<?php

namespace NhatHoa\App\Repositories\Interfaces;
use NhatHoa\App\Models\Product;
use NhatHoa\App\Services\InventoryService;
use NhatHoa\Framework\Core\Request;

interface ProductRepositoryInterface
{
    public function getAll(int $currentPage,int $limit,string $keyword) : array;
    public function getById(string $id) : Product|null;
    public function create(array $data,Request $request) : void;
    public function update(Product $product, array $data, Request $request, InventoryService $inventoryService) : void;
    public function delete(Product $product) : void;
    public function getLatest(int $number_of_products) : array;
}