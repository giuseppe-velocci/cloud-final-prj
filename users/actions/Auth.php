<?php
declare (strict_types=1);

namespace ImgAuth;

$mainFolder = dirname(__DIR__);
chdir(dirname($mainFolder));

require "./vendor/autoload.php";

//.. se c `e un token valido di condivisione: autorizza accesso


function isValidUser () : bool {
    //.. logic to check login validity

    return true;
}

// if 
if (! isValidUser()) {
    //
    // if invalid send unauthorized response
    http_response_code(401);
}

// render a .jpg
$file = basename($mainFolder) . $_SERVER['REQUEST_URI'];
$type = 'image/jpeg';
header('Content-Type:'.$type);
header('Content-Length: ' . filesize($file));
readfile($file);