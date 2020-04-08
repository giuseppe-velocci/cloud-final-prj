<?php
declare(strict_types=1);

namespace App\Controller\Access;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Controller\AbsController;
use App\Api\Registration\CreateUserApi;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Api\ApiPostRequestMiddleware;
use App\Middleware\Api\Api2HtmlResponseMiddleware;
use App\Middleware\Crypto\HashMiddleware;


class Register extends AbsController {
    use \App\Controller\Traits\SetMessageTrait;

    protected $apiAction;

    public function __construct(
        CreateUserApi $apiAction, 
        HashMiddleware $hashMiddleware,
        ApiPostRequestMiddleware $requestMiddleware,
        Api2HtmlResponseMiddleware $responseMiddleware
    ) {
        $this->createUser = $apiAction;

        $middlewares = [
            new InjectableMiddleware($hashMiddleware),
            new InjectableMiddleware($requestMiddleware),
            new InjectableMiddleware($responseMiddleware,
                function($response) {
                    $this->handleResponse($response);
                }
            ),
        ];

        parent::__construct($middlewares);
    }

    protected function controllerResponse($request) {
        return $this->createUser->execute($request);
    }


    protected function handleResponse($response) {
        $this->setResultMessage($response);
        header('Location: /register');
        exit;
    }
}