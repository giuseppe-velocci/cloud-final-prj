<?php
use App\Controller;

return [
    'GET /' => App\Controller\Views\Home::class,
    'GET /register'  => App\Controller\Views\Register::class,
    'POST /register' => App\Controller\Access\Register::class,
];
