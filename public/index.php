<?php

define("APP_PATH", dirname(dirname(__FILE__)));

define("PUBLIC_PATH",dirname(__FILE__));

define("VIEW_PATH", dirname(dirname(__FILE__)) . "/application/Views" );

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../application/Helpers.php';

$config = new NhatHoa\Framework\Factories\ConfigFactory(array(
    "type" =>"ini"
));

NhatHoa\Framework\Registry::set("config", $config->initialize());

$database = new NhatHoa\Framework\Factories\DatabaseFactory();

NhatHoa\Framework\Registry::set("database", $database->initialize());

$cache  = new NhatHoa\Framework\Factories\CacheFactory();

NhatHoa\Framework\Registry::set("cache", $cache->initialize());

$session = new NhatHoa\Framework\Factories\SessionFactory();

NhatHoa\Framework\Registry::set("session", $session->initialize());

$service_container = new NhatHoa\Framework\ServiceContainer();

NhatHoa\Framework\Registry::set("service_container", $service_container);

$response = new NhatHoa\Framework\Core\Response();

NhatHoa\Framework\Registry::set("response", $response);

require_once __DIR__ . '/../application/Events.php';

if(!file_exists(__DIR__ . '/../application/cache/auth.php')){
    $authorization = new NhatHoa\Framework\Core\Authorization();
    NhatHoa\Framework\Registry::set("authorization", $authorization);
    require_once __DIR__ . '/../application/Auths.php';
}else{
    $cachedAuth = require_once __DIR__ . '/../application/cache/auth.php';
    $auth = unserialize($cachedAuth);
    NhatHoa\Framework\Registry::set("authorization", $auth);
}

if(!file_exists(__DIR__ . '/../application/cache/router.php')){
    $router = new NhatHoa\Framework\Core\Router();
    require_once __DIR__ . "/../routes.php";
}else{
    $cachedRouter = require_once __DIR__ . '/../application/cache/router.php';
    $router = unserialize($cachedRouter);
}

$router->dispatch();