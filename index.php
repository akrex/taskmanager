<?php
//подключаем основные классы
require_once("config.php");
require_once("engine/controller.php");
require_once("engine/model.php");
require_once("engine/routemanager.php");
require_once("library/database.php");
require_once("library/helper.php");
require_once("library/useridentify.php");
require_once("library/validator.php");
require_once("engine/registry.php");
require_once("library/request.php");
require_once("library/corefunctions.php");
require_once("engine/loader.php");

$registry = new Registry();

$request = new Request();
$registry->set('request',$request);

$db = new DataBase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

$user = new UserIdentify($registry);
$registry->set('user', $user);

$loader = new Loader($registry);
$registry->set('loader',$loader);

//переадресовываем на нужные контроллеры и экшены
$rm = new RouteManager($registry);
$registry->set('routemanager',$rm);
$rm->RedirectToController($registry);