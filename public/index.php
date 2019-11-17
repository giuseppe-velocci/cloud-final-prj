<?php
chdir(dirname(__DIR__));

require "./vendor/autoload.php";

use App\Config\Env;

try{
  echo Env::get("DB_HOST");  
} catch(\Exception $e) {
    die($e->getMessage());
}
