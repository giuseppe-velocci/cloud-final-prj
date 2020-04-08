<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use App\Middleware\IMiddleware;
use App\Middleware\AbsRequestMiddleware;
use Psr\Http\Message\ServerRequestInterface;

class WithJwtRequestMiddleware extends AbsRequestMiddleware implements IMiddleware{
    protected function middlewareAction (ServerRequestInterface $request) {
        $cookies = $request->getCookieParams();

        // if jwt header does not exists -> 401
        if (! array_key_exists('token', $cookies)) {
            header('Location /error401');
            exit;
        }

        $headers = $request->getHeaders();
        $headers['Authorization'] = $cookies['token'];
        return $request->withHeaders($headers);
    }
}