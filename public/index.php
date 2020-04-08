<?php
declare(strict_types=1);

chdir(dirname(__DIR__));

require "./vendor/autoload.php";
use DI\ContainerBuilder;
use Zend\Diactoros\ServerRequestFactory;
// use App\Config\Env;
// use App\Db\ImagesDb;
// use App\Middleware\Auth\WithJwtRequestMiddleware;

$request = ServerRequestFactory::fromGlobals(
  $_SERVER,
  $_GET,
  $_POST,
  $_COOKIE,
  $_FILES
);

$builder = new ContainerBuilder();
$builder->addDefinitions('config/container.php');
$container = $builder->build();

$routes = require 'config/route.php';

$method = $request->getMethod();
preg_match('/\/\w+/', $request->getUri()->getPath(), $pathArray);
$path = $pathArray[0] ?? '/';

$murl   = sprintf("%s %s", $method, $path);
$controllerName = $routes[$murl] ?? App\Controller\Errors\Error404::class;

$controller = $container->get($controllerName);
$controller->execute($request);

/*
try{
  echo Env::get("DB_HOST");
} catch(\Exception $e) {
    die($e->getMessage());
}
*/