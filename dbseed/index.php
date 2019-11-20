<?php
declare(strict_types=1);

require "./vendor/autoload.php";

// -> "mongodb://localhost:27017"
// $driver = new MongoConnection('','');
use App\Db\MongoConnection;
use App\Config\Env;

/**
 * Script that will setup json files files that will be imported in mongo via
 * "mongoimport" cmd from the bash shell. 
 * 
 * This script will look for any other .php script inside this folder and will try to 
 * get from it an indexed array with values needed to populate the db.
 *  
 * All those scripts MUST return an indexed array with fields:
 * [db] = string, name of db
 * [collection] = string, name of current collection
 * [file] = string, filename
 * [data] = array, indexed array with data to be stored in collection
 */


/**
 * @TODO: pass args in shell for info about mongo connection in case root login is unavailable
 */


//---variable declarations
$importCmd = 'mongoimport --db {$db} --collection {$collection} --file {$file} --jsonArray';
try {
    $db = Env::get('DB_NAME');
} catch (\InvalidArgumentException $e) {
    die($e->getMessage());
}


//--- functions declaration

/**
 * function that returns the string to be run with shell_exec command in bash
 * @$cmd = string
 * @return = cmd output
 */
function cmdExec(string $cmd) : string {
    $output = shell_exec($cmd);
    if (is_null($output)) {
        throw \Error("Cmd failed: $cmd");
    }
    return $output;
}

/**
 * function that encodes an array of App\Db\GenericDbCollection to json string
 * @$data = array of App\Db\GenericDbCollection objects
 * @return = json string
 */
function array2json(array $data): string {
    $json = '[';
    foreach ($data AS $d) {
        $json .= json_encode($d->toArray());
    }
    $json .= ']';
    return $json;
}

/**
 * function that will create a .json file with all data to be imported in mongodb
 * @filename = string
 * @$data = string
 * @return = bool
 */
function data2file(string $filename, string $data): bool {
    $filename = __DIR__ . $filename;
    if (file_exists($filename)) {
        return false;
    }

    $file = fopen($filename, 'r');
    if (! $file) {
        throw new \Error('Cannot open file @' . __FILE__);
    }

    if (! fwrite($file, $data)) {
        throw new \Error('Cannot write file @' . __FILE__);
    }

    if (! fclose($file)) {
        throw new \Error('Cannot close file @' . __FILE__);   
    }

    return true;
}


//--- execution
$scripts = scandir(__DIR__);
$scripts = array_filter($scripts, function($x){
    $fileExtension = pathinfo($x, PATHINFO_EXTENSION);
    if ($fileExtension != 'php')
        return false;
    return $x != basename(__FILE__) && $x != '.' && $x != '..';
});


foreach ($scripts AS $s) {
    echo "\nRunning script: $s\n";
    $seeder = require $s;

    $importCmd = str_replace('{$db}', $seeder['db'], $importCmd);
    $importCmd = str_replace('{$collection}', $seeder['collection'], $importCmd);
    $importCmd = str_replace('{$file}', $seeder['file'], $importCmd);

    data2file($seeder['file'], array2json($seeder['data']));

//    echo cmdExec($importCmd) . "\n";

    /*
    try {
        $driver->setCurrentDb($db);
    } catch (\MongoDB\Driver\Exception\ConnectionException $e) {
        echo $e->getMessage();
        die("\n fatal error");
    }*/
}