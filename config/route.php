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
    'GET /photomaps'  => App\Controller\Views\PhotoMaps::class,
    'GET /photodetails'  => App\Controller\Views\PhotoDetails::class,
    'GET /shared'  => App\Controller\Views\Shared::class,
    

    'POST /uploadfile' => App\Controller\Actions\UploadFileAction::class,
    'POST /deletefile' => App\Controller\Actions\DeleteFileAction::class,
    'GET /getimage' => App\Controller\Actions\GetImageAction::class,
    'POST /photoshare'  => App\Controller\Actions\ShareImageAction::class,

    'GET /register'  => App\Controller\Views\Register::class,
    'POST /register' => App\Controller\Access\Register::class,
    
    'GET /error401' => App\Controller\Errors\Error401::class,

    // user api
    'POST /loginapi' => App\Controller\Api\User\LoginApiController::class,
    'POST /registerapi' => App\Controller\Api\User\RegisterApiController::class,
//    'POST /updateuserapi' => App\Controller\Api\User\UpdateUserApiController::class,
    'POST /validateloginapi' => App\Controller\Api\User\ValidateLoginApiController::class,

    // upload api
    'POST /uploadapi' => App\Controller\Api\Upload\UploadFileApiController::class,
    'POST /deletefileapi' => App\Controller\Api\Upload\DeleteFileApiController::class,
    'GET /getimageapi' => App\Controller\Api\Upload\GetImageApiController::class,

];
