<?php
chdir(dirname(__DIR__));

echo __DIR__ . '<br/>';

require "./vendor/autoload.php";

use App\Config\Env;

echo '<img src="user/img.jpeg"/>';

try{
  echo Env::get("DB_HOST");  
} catch(\Exception $e) {
    die($e->getMessage());
}