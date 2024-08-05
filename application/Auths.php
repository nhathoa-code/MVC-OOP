<?php

use NhatHoa\App\Authorizations\BeforeAuth;
use NhatHoa\Framework\Registry;
use NhatHoa\App\Authorizations\CategoryAuth;
use NhatHoa\App\Authorizations\ProductAuth;
use NhatHoa\App\Authorizations\CouponAuth;
use NhatHoa\App\Authorizations\InvoiceAuth;
use NhatHoa\App\Authorizations\OrderAuth;
use NhatHoa\App\Authorizations\PermissionAuth;
use NhatHoa\App\Authorizations\RoleAuth;
use NhatHoa\App\Authorizations\UserAuth;
use NhatHoa\App\Authorizations\StoreAuth;

$auth = Registry::get("authorization");

/**
 * run before all authorizations check
 */
$auth->before([BeforeAuth::class,"authorize"]);

/**
 * authorizations for category resource
 */
$auth->for("create-category",[CategoryAuth::class,"create"]);

$auth->for("read-category",[CategoryAuth::class,"read"]);

$auth->for("update-category",[CategoryAuth::class,"update"]);

$auth->for("delete-category",[CategoryAuth::class,"delete"]);

/**
 * authorizations for product resource
 */
$auth->for("create-product",[ProductAuth::class,"create"]);

$auth->for("read-product",[ProductAuth::class,"read"]);

$auth->for("update-product",[ProductAuth::class,"update"]);

$auth->for("delete-product",[ProductAuth::class,"delete"]);

/**
 * authorizations for coupon resource
 */
$auth->for("create-coupon",[CouponAuth::class,"create"]);

$auth->for("read-coupon",[CouponAuth::class,"read"]);

$auth->for("update-coupon",[CouponAuth::class,"update"]);

$auth->for("delete-coupon",[CouponAuth::class,"delete"]);

/**
 * authorizations for order resource
 */
$auth->for("read-order",[OrderAuth::class,"read"]);

$auth->for("update-order",[OrderAuth::class,"update"]);

$auth->for("delete-order",[OrderAuth::class,"delete"]);

/**
 * authorizations for user resource
 */
$auth->for("create-user",[UserAuth::class,"create"]);

$auth->for("read-user",[UserAuth::class,"read"]);

$auth->for("update-user",[UserAuth::class,"update"]);

$auth->for("delete-user",[UserAuth::class,"delete"]);

/**
 * authorizations for store resource
 */
$auth->for("create-store",[StoreAuth::class,"create"]);

$auth->for("read-store",[StoreAuth::class,"read"]);

$auth->for("update-store",[StoreAuth::class,"update"]);

$auth->for("delete-store",[StoreAuth::class,"delete"]);

/**
 * authorizations for invoice resource
 */
$auth->for("create-invoice",[InvoiceAuth::class,"create"]);

$auth->for("read-invoice",[InvoiceAuth::class,"read"]);

$auth->for("delete-invoice",[InvoiceAuth::class,"delete"]);

/**
 * authorizations for role resource
 */
$auth->for("create-role",[RoleAuth::class,"create"]);

$auth->for("read-role",[RoleAuth::class,"read"]);

$auth->for("update-role",[RoleAuth::class,"update"]);

$auth->for("delete-role",[RoleAuth::class,"delete"]);

/**
 * authorizations for permission resource
 */
$auth->for("create-permission",[PermissionAuth::class,"create"]);

$auth->for("read-permission",[PermissionAuth::class,"read"]);

$auth->for("update-permission",[PermissionAuth::class,"update"]);

$auth->for("delete-permission",[PermissionAuth::class,"delete"]);