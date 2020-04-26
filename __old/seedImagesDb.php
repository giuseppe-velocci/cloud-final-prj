<?php
declare(strict_types=1);

chdir(__DIR__);

require "../vendor/autoload.php";

use App\Db\Images;
use App\Config\Env;

try {
    $db = Env::get('DB_NAME');
//    $dbhost = Env::get('DB_HOST');
} catch (\InvalidArgumentException $e) {
    die($e->getMessage());
}
$collection = 'images';
$filename   = str_replace('.php', '.json', basename(__FILE__));


// i will resturn ONLY an array of values;
$seeder = [
    'db'         => $db,
    'collection' => $collection,
    'file'       => $filename,
    'data'       => [
        new Images('1', 'blob/img.url', 'user125', ["person", "sky"]),
        new Images('2', 'blob/img-2.url', 'user10', ["person", "sea"])
    ]
];
return $seeder;