<?php
declare(strict_types=1);

namespace App\Controller\Access;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Api\Registration\CreateUser;
use App\Middleware\IHttpRequestMiddleware;
use App\Middleware\IHttpResponseMiddleware;

class Register implements \App\Controller\IController {
    protected $createUser;
    protected $requestMiddleware;
    protected $responseMiddleware;

    public function __construct(
        CreateUser $createUser, 
        IHttpRequestMiddleware $requestMiddleware,
        IHttpResponseMiddleware $responseMiddleware
    )
    {
        $this->plates = $plates;
        $this->createUser = $createUser;
        $this->requestMiddleware = $requestMiddleware;
        $this->responseMiddleware = $responseMiddleware;
    }

    protected function getRedirectView(ResponseInterface $response):string {
        if ($response->getStatusCode() == 200) {
            return '/register';
        } 

        return '/';
    }


    public function execute(ServerRequestInterface $request) :void {
        $data = $this->requestMiddleware->handle(
            $request,
            null
        );

        $response = $this->responseMiddleware->handle (
            $this->createUser->execute($data),
            null
        );
        
        header('Location: '.$this->getRedirectView($response));
    }
}