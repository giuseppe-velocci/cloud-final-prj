<?php
declare(strict_types=1);

chdir(__DIR__);

require "../vendor/autoload.php";

use App\Db\MongoConnection;
use App\Db\BaseMapObject;
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
    $db   = Env::get('DB_NAME');
    $user = Env::get('DB_USER');
    $pwd  = Env::get('DB_PWD');
    // -> "mongodb://localhost:27017"
    $driver = new MongoConnection('','');

} catch (\InvalidArgumentException $e) {
    die($e->getMessage());
}


//--- functions declaration

/**
 * function that returns the string to be run with shell_exec command in bash
 * @param $cmd = string
 * @return = cmd output
 */
function cmdExec(string $cmd) : int {
    // $output = shell_exec($cmd);
    exec($cmd, $output, $returnVal);
var_dump($returnVal);
    if ($returnVal > 0) {
        throw new \Error("Cmd failed: $cmd");
    }
    return $returnVal;
}

/**
 * function that encodes an array of App\Db\AbsDbCollection to json string
 * @param $data = array of App\Db\AbsDbCollection objects
 * @return = json string
 */
function array2json(array $data): string {
    $json = '[';
    foreach ($data AS $d) {
        if ($d instanceof BaseMapObject)
            $d = $d->toArray();
        
        $json .= json_encode($d) . ',';
    }
    $json = substr($json, 0, -1);
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
    $filename = __DIR__ . "/$filename";
    if (file_exists($filename)) {
        return false;
    }

    $file = fopen($filename, 'w');
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

    $localImportCmd = $importCmd;
    $localImportCmd = str_replace('{$db}', $seeder['db'], $localImportCmd);
    $localImportCmd = str_replace('{$collection}', $seeder['collection'], $localImportCmd);
    $localImportCmd = str_replace('{$file}', $seeder['file'], $localImportCmd);

    echo "\nExecuting: $localImportCmd\n";

    data2file($seeder['file'], array2json($seeder['data']));

    echo "\nAwaiting connection...\n";

    
    try {
        $driver->getConnection();
        cmdExec($localImportCmd) . "\n";

        echo 'Complete json import';
        if (!unlink($seeder['file'])) {
            echo "\nJson file deletion for ".$seeder['file']." failed!\n";
        }

    } catch (\MongoDB\Driver\Exception\ConnectionException $e) {
        echo $e->getMessage();
        die("\n fatal error");
    }
}


// then add dbuser with a js script to be evaluated
$script = file_get_contents('createUser.js');
$script = str_replace('$db', $db, $script);
$script = str_replace('$user', $user, $script);
$script = str_replace('$pwd', $pwd, $script);

echo $script;

$evalCmd = sprintf("mongo --eval '%s'", $script);
cmdExec($evalCmd);

echo "\nDb user created successfully.";
echo "\nDone.";