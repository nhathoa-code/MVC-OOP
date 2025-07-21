<?php

use NhatHoa\App\Middlewares\Admin;
use NhatHoa\App\Middlewares\User;

use NhatHoa\App\Controllers\AdminController;
use NhatHoa\App\Controllers\AttributeController;
use NhatHoa\App\Controllers\CategoryController;
use NhatHoa\App\Controllers\ProductController;
use NhatHoa\App\Controllers\ClientController;
use NhatHoa\App\Controllers\CartController;
use NhatHoa\App\Controllers\CouponController;
use NhatHoa\App\Controllers\StoreController;
use NhatHoa\App\Controllers\SizeChartController;
use NhatHoa\App\Controllers\OrderController;
use NhatHoa\App\Controllers\UserController;
use NhatHoa\App\Controllers\AuthController;
use NhatHoa\App\Controllers\CustomerController;
use NhatHoa\App\Controllers\LocationController;
use NhatHoa\App\Controllers\PermissionGroupController;
use NhatHoa\App\Controllers\PosController;
use NhatHoa\App\Controllers\RoleController;
use NhatHoa\App\Controllers\WishListController;

$router->get("/",[ClientController::class,"index"]);

$router->get("/collection/{categories}",[ClientController::class,"collection"]);

$router->get("/product/detail/{id}",[ClientController::class,"product"]);

$router->get("/search",[ClientController::class,"search"]);

$router->get("/stores/search",[ClientController::class,"searchStores"]);

$router->get("/checkout",[ClientController::class,"checkoutView"]);

$router->post("/checkout",[ClientController::class,"checkout"]);

$router->post("/apply/coupon",[ClientController::class,"applyCoupon"]);

$router->post("/apply/point",[ClientController::class,"applyPoint"]);

$router->get("/order/track",[ClientController::class,"orderTrack"]);

$router->get("/vnpay/confirm",[ClientController::class,"vnpayConfirm"]);

$router->get("/user/{part}",[AuthController::class,"user"], User::class);

$router->group("wishlist",function() use($router){
    $router->post("/add",[WishListController::class,"add"]);
    $router->post("/remove",[WishListController::class,"remove"]);
});

$router->group("auth",function() use($router){
    $router->get("/login",[AuthController::class,"loginView"]);
    $router->post("/login",[AuthController::class,"login"]);
    $router->get("/logout",[AuthController::class,"logout"]);
    $router->get("/registry",[AuthController::class,"registryView"]);
    $router->post("/registry",[AuthController::class,"registry"]);
    $router->post("/retrievepassword",[AuthController::class,"retrievePassword"]);
    $router->get("/email/verify",[AuthController::class,"authVerify"]);
    $router->get("/forgotpassword",[AuthController::class,"forgotPassword"]);
    $router->get("/resetpassword",[AuthController::class,"resetPasswordView"]);
    $router->post("/resetpassword",[AuthController::class,"resetPassword"]);
});

$router->group("user",function() use($router){
    $router->post("/address/add",[UserController::class,"addAddress"]);
    $router->get("/user/address/edit/{id}",[UserController::class,"editAddress"]);
    $router->post("/address/delete/{address_id}",[UserController::class,"deleteAddress"]);
    $router->post("/address/update/{address_id}",[UserController::class,"updateAddress"]);
    $router->post("/profile/update",[UserController::class,"updateProfile"]);
    $router->post("/password/update",[UserController::class,"updatePassword"]);
},User::class);

$router->group("cart",function() use($router){ 
    $router->get("/",[CartController::class,"index"]);
    $router->post("/add",[CartController::class,"add"]);
    $router->post("/delete/{index}",[CartController::class,"delete"]);
    $router->post("/update",[CartController::class,"update"]);
});

$router->group("admin",function() use($router){

    $router->get("/login",[AdminController::class,"loginView"],["without"=>[Admin::class]]);

    $router->post("/login",[AdminController::class,"login"],["without"=>[Admin::class]]);

    $router->get("/logout",[AdminController::class,"logout"]);

    $router->get("/",[AdminController::class,"index"]);

    $router->get("/statistical/{type}",[AdminController::class,"statistical"]);

    $router->group("category",function() use($router){
        $router->get("/",[CategoryController::class,"index"]);
        $router->post("/add",[CategoryController::class,"add"]);
        $router->post("/delete/{id}",[CategoryController::class,"delete"]);
        $router->get("/edit/{id}",[CategoryController::class,"edit"]);
        $router->post("/update/{id}",[CategoryController::class,"update"]);
        $router->get("/{id}/attributes",[CategoryController::class,"getAttributes"]);
    });

    $router->group("attribute",function() use($router){
        $router->get("/",[AttributeController::class,"index"]);
        $router->post("/store",[AttributeController::class,"store"]);
        $router->post("/delete/{id}",[AttributeController::class,"deleteAttribute"]);
        $router->get("/edit/{id}",[AttributeController::class,"editAttribute"]);
        $router->post("/update/{id}",[AttributeController::class,"updateAttribute"]);
        $router->get("/{id}/values",[AttributeController::class,"getValues"]);
        $router->post("/{id}/value/add",[AttributeController::class,"addValue"]);
        $router->get("/{attribute_id}/value/{value_id}/edit",[AttributeController::class,"editValue"]);
        $router->post("/{attribute_id}/value/{value_id}/update",[AttributeController::class,"updateValue"]);
        $router->post("/{attribute_id}/value/{value_id}/delete",[AttributeController::class,"deleteValue"]);
    });

    $router->group("product",function() use($router){
        $router->get("/",[ProductController::class,"index"]);
        $router->get("/add",[ProductController::class,"addView"]);
        $router->post("/add",[ProductController::class,"add"]);
        $router->get("/edit/{id}",[ProductController::class,"edit"]);
        $router->post("/update/{id}",[ProductController::class,"update"]);
        $router->post("/delete/{id}",[ProductController::class,"delete"]);
        $router->get("/export/excel",[ProductController::class,"exportExcel"]);
    });

    $router->group("coupon",function() use($router){
        $router->get("/",[CouponController::class,"index"]);
        $router->post("/add",[CouponController::class,"add"]);
        $router->post("/delete/{id}",[CouponController::class,"delete"]);
        $router->get("/edit/{id}",[CouponController::class,"edit"]);
        $router->post("/update/{id}",[CouponController::class,"update"]);
    });

    $router->group("order",function() use($router){
        $router->get("/",[OrderController::class,"index"]);
        $router->get("/delete/{id}",[OrderController::class,"delete"]);
        $router->get("/{id}",[OrderController::class,"order"]);
        $router->post("/status/{id}",[OrderController::class,"update"]);
    });

    $router->group("store",function() use($router){
        $router->get("/",[StoreController::class,"index"]);
        $router->post("/add",[StoreController::class,"add"]);
        $router->post("/delete/{id}",[StoreController::class,"delete"]);
        $router->get("/edit/{id}",[StoreController::class,"edit"]);
        $router->post("/update/{id}",[StoreController::class,"update"]);
        $router->get("/{id}/inventory/add",[StoreController::class,"addInventoryView"]);
        $router->get("/findproduct/{product_id}",[StoreController::class,"findProduct"]);
        $router->post("/{id}/inventory/add",[StoreController::class,"addInventory"]);
        $router->get("/{id}/inventory",[StoreController::class,"inventory"]);
        $router->get("/{id}/inventory/export/excel",[StoreController::class,"exportExcel"]);
        $router->get("/{id}/inventory/product/{product_id}/edit",[StoreController::class,"productInventory"]);
        $router->post("/{id}/inventory/product/{product_id}/edit",[StoreController::class,"updateProductInventory"]);
        $router->post("/{id}/inventory/product/{product_id}/delete",[StoreController::class,"deleteProductInventory"]);
    });

    $router->group("location",function() use($router){
        $router->get("/province",[LocationController::class,"index"]);
        $router->post("/province/add",[LocationController::class,"addProvince"]);
        $router->get("/province/edit/{id}",[LocationController::class,"editProvince"]);
        $router->post("/province/update/{id}",[LocationController::class,"updateProvince"]);
        $router->post("/province/delete/{id}",[LocationController::class,"deleteProvince"]);
        $router->get("/province/{id}/districts",[LocationController::class,"districts"]);
        $router->post("/province/{id}/district/add",[LocationController::class,"addDistrict"]);
        $router->get("/province/{province_id}/district/{district_id}/edit",[LocationController::class,"editDistrict"]);
        $router->post("/province/{province_id}/district/{district_id}/update",[LocationController::class,"updateDistrict"]);
        $router->post("/province/{province_id}/district/{district_id}/delete",[LocationController::class,"deleteDistrict"]);
    });

    $router->group("pos",function() use($router){
        $router->get("/invoice",[PosController::class,"invoices"]);
        $router->get("/invoice/{id}/print",[PosController::class,"generateInvoice"]);
        $router->get("/invoice/export/excel",[PosController::class,"exportExcel"]);
        $router->get("/invoice/{invoice_id}",[PosController::class,"showInvoice"]);
        $router->post("/invoice/{invoice_id}/delete",[PosController::class,"deleteInvoice"]);
        $router->get("/sale",[PosController::class,"createSaleView"]);
        $router->post("/sale/create",[PosController::class,"createSale"]);
        $router->get("/store/{store_id}/products",[PosController::class,"getProductsFromStore"]);
    });

    $router->group("size-chart",function() use($router){
        $router->get("/",[SizeChartController::class,"index"]);
        $router->get("/add",[SizeChartController::class,"addView"]);
        $router->post("/add",[SizeChartController::class,"add"]);
        $router->post("/delete/{id}",[SizeChartController::class,"delete"]);
        $router->get("/edit/{id}",[SizeChartController::class,"edit"]);
        $router->post("/update/{id}",[SizeChartController::class,"update"]);
    });
    
    $router->group("user",function() use($router){
        $router->get("/",[UserController::class,"index"]);
        $router->get("/add",[UserController::class,"addView"]);
        $router->post("/add",[UserController::class,"addUser"]);
        $router->get("/edit/{id}",[UserController::class,"editView"]);
        $router->post("/update/{id}",[UserController::class,"update"]);
        $router->post("/delete/{id}",[UserController::class,"delete"]);
        $router->get("/profile/{id}",[UserController::class,"profile"]);
    });

    $router->group("customer",function() use($router){
        $router->get("/",[CustomerController::class,"index"]);
        $router->get("/add",[CustomerController::class,"addView"]);
        $router->post("/add",[CustomerController::class,"add"]);
        $router->get("/edit/{id}",[CustomerController::class,"edit"]);
        $router->post("/update/{id}",[CustomerController::class,"update"]);
        $router->get("/fetch",[CustomerController::class,"fetchCustomer"]);
        $router->get("/{id}/purchase/history",[CustomerController::class,"purchaseHistory"]);
        $router->post("/delete/{id}",[CustomerController::class,"delete"]);
        $router->get("/export/excel",[CustomerController::class,"exportExcel"]);
    });

    $router->group("permission-group",function() use($router){
        $router->get("/",[PermissionGroupController::class,"index"]);
        $router->post("/add",[PermissionGroupController::class,"add"]);
        $router->get("/edit/{id}",[PermissionGroupController::class,"edit"]);
        $router->post("/update/{id}",[PermissionGroupController::class,"update"]);
        $router->post("/delete/{id}",[PermissionGroupController::class,"delete"]);
        $router->get("/{group_id}/permissions",[PermissionGroupController::class,"permissions"]);
        $router->post("/{group_id}/permission/add",[PermissionGroupController::class,"addPermission"]);
        $router->get("/{group_id}/permission/edit/{permission_id}",[PermissionGroupController::class,"editPermission"]);
        $router->post("/{group_id}/permission/update/{permission_id}",[PermissionGroupController::class,"updatePermission"]);
        $router->post("/{group_id}/permission/delete/{permission_id}",[PermissionGroupController::class,"deletePermission"]);
    });

    $router->group("role",function() use($router){
        $router->get("/list",[RoleController::class,"index"]);
        $router->get("/add",[RoleController::class,"addView"]);
        $router->post("/add",[RoleController::class,"add"]);
        $router->get("/edit/{id}",[RoleController::class,"edit"]);
        $router->post("/update/{id}",[RoleController::class,"update"]);
        $router->post("/delete/{id}",[RoleController::class,"delete"]);
    });

    $router->get("/(.*)",[AdminController::class,"notFound"]);

}, Admin::class);

$router->get(".*",[ClientController::class,"notFound"]);