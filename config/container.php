<?php

use League\Plates\Engine;
use Psr\Container\ContainerInterface;

use App\Db\User;
use App\Db\MongoConnection;
use Zend\Diactoros\ResponseFactory;
//
use App\Middleware\IHttpRequestMiddleware;
use App\Middleware\ApiRequestMiddleware;
use App\Middleware\IHttpResponseMiddleware;
use App\Middleware\ApiResponseMiddleware;
use App\Helpers\HttpResponse;


return [
    'view_path' => 'templates',
    
    Engine::class => function(ContainerInterface $c) {
        return new Engine($c->get('view_path'));
    },

    MongoConnection::class => function(ContainerInterface $c) {
        return new MongoConnection();
    },

    User::class => function(ContainerInterface $c) {
        return new User($c->get(MongoConnection::class));
    },

    'ApiRequestMiddlewareDefaultCallback' => function () {
        return function ($r) {
            return json_encode(implode('',$r));
        };
    },
    IHttpRequestMiddleware::class => function(ContainerInterface $c) {
        return new ApiRequestMiddleware(
            $c->get('ApiRequestMiddlewareDefaultCallback')
        );
    },

    'ApiResponseMiddlewareDefaultCallback' => function () {
        return function ($json) {
            $jsonObj = json_decode($json);
            $response = new ResponseFactory();
            try {
                return $response->createResponse($jsonObj->code, $jsonObj->message);
            } catch (\InvalidArgumentException $e) {
                return $response->createResponse(500, $e);
            }
        };
    },
    IHttpResponseMiddleware::class => function(ContainerInterface $c) {
        return new ApiResponseMiddleware(
            $c->get('ApiResponseMiddlewareDefaultCallback')
        );
    },
];