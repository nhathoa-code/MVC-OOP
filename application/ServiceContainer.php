<?php

use NhatHoa\Framework\Registry;
use NhatHoa\App\Repositories\Interfaces\CategoryRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\ProductRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\SizeChartRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\CouponRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\OrderRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\ProvinceRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\PermissionGroupRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\RoleRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\UserRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\StoreRepositoryInterface;
use NhatHoa\App\Repositories\Interfaces\CustomerRepositoryInterface;
use NhatHoa\App\Repositories\PermissionGroupRepository;
use NhatHoa\App\Repositories\CategoryRepository;
use NhatHoa\App\Repositories\CouponRepository;
use NhatHoa\App\Repositories\CustomerRepository;
use NhatHoa\App\Repositories\OrderRepository;
use NhatHoa\App\Repositories\ProductRepository;
use NhatHoa\App\Repositories\ProvinceRepository;
use NhatHoa\App\Repositories\RoleRepository;
use NhatHoa\App\Repositories\SizeChartRepository;
use NhatHoa\App\Repositories\StoreRepository;
use NhatHoa\App\Repositories\UserRepository;

$service_container = Registry::get("service_container");

$service_container->set(CategoryRepositoryInterface::class,function(){
    return new CategoryRepository();
});

$service_container->set(ProductRepositoryInterface::class,function(){
    return new ProductRepository();
});

$service_container->set(SizeChartRepositoryInterface::class,function(){
    return new SizeChartRepository();
});

$service_container->set(CouponRepositoryInterface::class,function(){
    return new CouponRepository();
});

$service_container->set(OrderRepositoryInterface::class,function(){
    return new OrderRepository();
});

$service_container->set(ProvinceRepositoryInterface::class,function(){
    return new ProvinceRepository();
});

$service_container->set(RoleRepositoryInterface::class,function(){
    return new RoleRepository();
});

$service_container->set(PermissionGroupRepositoryInterface::class,function(){
    return new PermissionGroupRepository();
});

$service_container->set(UserRepositoryInterface::class,function(){
    return new UserRepository();
});

$service_container->set(StoreRepositoryInterface::class,function(){
    return new StoreRepository();
});

$service_container->set(CustomerRepositoryInterface::class,function(){
    return new CustomerRepository();
});