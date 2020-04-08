<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use App\Middleware\IMiddleware;
use App\Middleware\AbsRequestMiddleware;
use Psr\Http\Message\ServerRequestInterface;

class ApiNeedsAuthMiddleware extends AbsRequestMiddleware implements IMiddleware{
    protected function middlewareAction (ServerRequestInterface $request) {
        $headers = $request->getHeaders();
        
        // if jwt GET parameter does not exists -> 401
        if (! array_key_exists('Authorization', $headers)) {
            header('Location: /error401');
            exit;
        }

        // else make a call to ValidateLoginApi (from a controller)

        // if response == 200 -> return request as is

        // if response != 200 again 401 page
    }
}