<?php
chdir(dirname(__DIR__));

echo __DIR__ . '<br/>';
var_dump($_SERVER);

require "./vendor/autoload.php";

use App\Config\Env;

try{
  echo Env::get("DB_HOST");  
} catch(\Exception $e) {
    die($e->getMessage());
}
echo 'HOME';