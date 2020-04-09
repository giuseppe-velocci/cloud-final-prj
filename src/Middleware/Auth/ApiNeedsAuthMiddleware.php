<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use App\Middleware\IMiddleware;
use App\Middleware\AbsRequestMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use App\Controller\Access\ValidateLogin;
use App\Helper\CryptMsg;


class ApiNeedsAuthMiddleware extends AbsRequestMiddleware implements IMiddleware{
    protected $apiCaller;
    protected $crypt;

    public function __construct(
        ValidateLogin $apiCaller,
        CryptMsg $crypt
    ) {
        $this->apiCaller = $apiCaller;
        $this->crypt = $crypt;
    }

    protected function middlewareAction (ServerRequestInterface $request) {
        $headers = $request->getHeaders();
        $cookies = $request->getCookieParams();
/*

*/
        if (! isset($cookies['token']) && ! array_key_exists('Authorization', $headers)) {
            header('Location: /error401');
            exit;
        }

        if (! isset($headers['Authorization'])) {
            $request = $request->withHeader(
                'Authorization', 
                sprintf('Bearer %s', $this->crypt->decrypt($cookies['token'], $this->crypt::nonce()))
            );
        } 

        $this->apiCaller->execute($request);
        
        // if response == 200 -> return initial request
        return $request; 
    }
}