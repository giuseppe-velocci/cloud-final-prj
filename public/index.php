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

use App\Img\ImgValidator;
use App\Img\Upload;
$u = new Upload('');

var_dump(Upload::UPLOAD_INPUT_NAME);
var_dump(dirname(__DIR__) .'/users/index.html');

$_FILES[Upload::UPLOAD_INPUT_NAME]['tmp_name'] = dirname(__DIR__) .'/users/index.html';
$_FILES[Upload::UPLOAD_INPUT_NAME]['name'] = dirname(__DIR__) .'/users/index.html';
$_FILES[Upload::UPLOAD_INPUT_NAME]['error'] = 0;

$u->uploadImg();


$iv = new ImgValidator(['image/jpeg']);
echo App\Img\ImgValidator::MAX_FILENAME_LEN;


var_dump(
  $iv->isValidFilename( $_FILES[Upload::UPLOAD_INPUT_NAME]['tmp_name'])
);