<?php

namespace NhatHoa\App\Repositories\Interfaces;
use NhatHoa\App\Models\Order;
use NhatHoa\App\Models\Coupon;
use NhatHoa\App\Services\CartService;
use NhatHoa\App\Services\InventoryService;
use NhatHoa\App\Services\UserService;

interface OrderRepositoryInterface
{
    public function getAll(string $status,int $limit,int $currentPage,string $keyword) : array;
    public function getById(int $id) : Order|null;
    public function create(array $data,Coupon|null $coupon,int $V_point,CartService $cartService,InventoryService $inventoryService) : Order;
    public function update(Order $order,string $status,UserService $userService) : void;
    public function delete(Order $order) : void;
}