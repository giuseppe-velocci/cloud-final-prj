<?php
declare(strict_types=1);

namespace App\Controller\Access;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Helper\HashMsg;
use App\Api\Login\LoginApi;
use App\Middleware\Api\ApiPostRequestMiddleware;
use App\Middleware\Api\Api2HtmlResponseMiddleware;
use App\Middleware\Cookie\CookieMiddleware;
use App\Controller\AbsController;
use App\Middleware\InjectableMiddleware;

class Login extends AbsController{
    use \App\Controller\Traits\SetMessageTrait;

    protected $apiAction;

    public function __construct(
        LoginApi $apiAction, 
        ApiPostRequestMiddleware $apiRequestMiddleware,
        Api2HtmlResponseMiddleware $apiResponseMiddleware
    ) {
        $this->apiAction = $apiAction;

        $middlewares = [
            new InjectableMiddleware($apiRequestMiddleware),
            new InjectableMiddleware($apiResponseMiddleware,
                function($response) {
                    $this->handleResponse($response);
                }
            ),
        ];

        parent::__construct($middlewares);
    }

    protected function controllerResponse($request) {
        return $this->apiAction->execute($request);
    }

    protected function handleResponse($response) {
        if ($response->getStatusCode() != 200) {
            $this->setResultMessage($response);
            $landing = 'login';
        } else {
            $headers = $response->getHeaders();
            $jwt = str_replace('Bearer ', '', $headers['Authorization'][0]);
            setcookie('token', $jwt);
            $landing = 'dashboard';
        }

        header(sprintf('Location: /%s', $landing));
        exit;
    }
}