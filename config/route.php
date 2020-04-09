<?php
use App\Controller;

return [
    'GET /' => App\Controller\Views\Home::class,
    
    'GET /login'  => App\Controller\Views\Login::class,
    'POST /login' => App\Controller\Access\Login::class,
    'GET /logout'  => App\Controller\Access\Logout::class,

    'POST /validatelogin' => App\Controller\Access\ValidateLogin::class,

    'GET /dashboard'  => App\Controller\Views\Dashboard::class,
    'GET /photomanager'  => App\Controller\Views\PhotoManager::class,
    'POST /uploadfile' => App\Controller\Actions\UploadFileAction::class,


    'GET /register'  => App\Controller\Views\Register::class,
    'POST /register' => App\Controller\Access\Register::class,
    
    'GET /error401' => App\Controller\Errors\Error401::class,

    // user api
    'POST /loginapi' => App\Controller\Api\LoginApiController::class,
    'POST /registerapi' => App\Controller\Api\RegisterApiController::class,
    'POST /updateuserapi' => App\Controller\Api\UpdateUserApiController::class,
    'POST /validateloginapi' => App\Controller\Api\ValidateLoginApiController::class,

    // upload api
    'POST /uploadapi' => App\Controller\Api\Upload\UploadFileApiController::class,
];
