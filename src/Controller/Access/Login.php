<?php
declare(strict_types=1);

namespace App\Controller\Access;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Helper\HashMsg;
use App\Api\Login\LoginApi;
use App\Middleware\Api\ApiPostRequestMiddleware;
use App\Middleware\Api\Api2HtmlResponseMiddleware;
use App\Controller\AbsController;
use App\Middleware\InjectableMiddleware;

class Login extends AbsController implements \App\Controller\IController {
    use \App\Controller\Traits\SetMessageTrait;
    use \App\Controller\Traits\SetCookieTrait;

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
           $response = $this->setLoginCookies($response);
           $landing = 'dashboard';
        }

        header(sprintf('Location: /%s', $landing));
        exit;
    }
}