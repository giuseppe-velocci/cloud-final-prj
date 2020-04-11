<?php
declare(strict_types=1);

namespace App\Controller\Access;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Api\Login\LoginApi;
use App\Controller\AbsController;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Api\ApiPostRequestMiddleware;
use App\Middleware\Api\Api2HtmlResponseMiddleware;
use App\Middleware\Cookie\SetCookieMiddleware;
use App\Middleware\Html\ResponseOutputMiddleware;


class Login extends AbsController implements \App\Controller\IController {
    use \App\Traits\SetMessageTrait;
//    use \App\Traits\SetCookieTrait;

    protected $apiAction;

    public function __construct(
        LoginApi $apiAction, 
        ApiPostRequestMiddleware $apiRequestMiddleware,
        Api2HtmlResponseMiddleware $apiResponseMiddleware,
        SetCookieMiddleware $cookieMiddleware,
        ResponseOutputMiddleware $outputMiddleware
    ) {
        $this->apiAction = $apiAction;

        $middlewares = [
            new InjectableMiddleware($apiRequestMiddleware),
            new InjectableMiddleware($apiResponseMiddleware),
            new InjectableMiddleware($cookieMiddleware),
            new InjectableMiddleware($outputMiddleware,
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
        //   $response = $this->setLoginCookies($response);
           $landing = 'dashboard';
        }

        header(sprintf('Location: /%s', $landing));
        exit;
    }
}