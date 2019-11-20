<?php
declare(strict_types=1);

chdir(dirname(__DIR__));

require "./vendor/autoload.php";

use App\Config\Env;
use App\Db\ImagesDb;

try{
  echo Env::get("DB_HOST");
} catch(\Exception $e) {
    die($e->getMessage());
}