<?php
declare(strict_types=1);

namespace App\Controller\Access;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Api\Registration\CreateUser;
use App\Middleware\Api\ApiPostRequestMiddleware;
use App\Middleware\Api\Api2HttpResponseMiddleware;
use App\Middleware\Cookie\CookieMiddleware;

class Register implements \App\Controller\IController{
    protected $createUser;
    protected $requestMiddleware;
    protected $responseMiddleware;
    protected $cookieMiddleware;


    public function __construct(
        CreateUser $createUser, 
        ApiPostRequestMiddleware $requestMiddleware,
        Api2HttpResponseMiddleware $responseMiddleware,
        CookieMiddleware $cookieMiddleware
    )
    {
        $this->createUser = $createUser;
        $this->requestMiddleware = $requestMiddleware;
        $this->responseMiddleware = $responseMiddleware;
        $this->cookieMiddleware = $cookieMiddleware;
    }


    protected function getRedirectView(ResponseInterface $response):string {
        if ($response->getStatusCode() == 200) {
            return '/register';
        } 

        return '/register';
    }


    public function execute(ServerRequestInterface $request) :void {
        // create a variable that stores the execute function
        $createExec = [$this->createUser, 'execute'];

        $jsonResponse = $this->requestMiddleware->handle(
            $request,
            $createExec
        );

        $response = $this->responseMiddleware->handle(
            $jsonResponse
        );

        // setcookie('message', $response->getReasonPhrase());
        $this->cookieMiddleware->addCookies(
            ['message' => $response->getReasonPhrase()]
        );
        $this->cookieMiddleware->handle(
            $request
        );

        header('Location: '.$this->getRedirectView($response));
    }
}