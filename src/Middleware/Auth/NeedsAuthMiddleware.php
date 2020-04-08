<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use App\Middleware\IMiddleware;
use App\Middleware\AbsRequestMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use App\Controller\Access\ValidateLogin;

class NeedsAuthMiddleware extends AbsRequestMiddleware implements IMiddleware{
    protected $apiCaller;

    public function __construct(
        ValidateLogin $apiCaller
    ) {
        $this->apiCaller = $apiCaller;
    }
    
    protected function middlewareAction (ServerRequestInterface $request) {
        $cookies = $request->getCookieParams();
        
        // if token cookie does not exists -> 401
        if (! isset($cookies['token'])) {
            header('Location: /error401');
            exit;
        }

        // make a call to ValidateLoginApi (from a controller)
        $postRequest = $request->withMethod('POST');
        $postRequest = $postRequest->withHeader(
            'Authorization', 
            sprintf('Bearer %s', $cookies['token'])
        );

        $this->apiCaller->execute($postRequest);
        
        // if response == 200 -> return initial request
        return $request;        
    }
}