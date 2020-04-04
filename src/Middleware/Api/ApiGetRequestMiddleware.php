<?php

declare(strict_types=1);

namespace App\Middleware\Api;

use Psr\Http\Message\ServerRequestInterface;

class ApiGetRequestMiddleware extends AbsApiRequestMiddleware{
    protected function middlewareAction (ServerRequestInterface $request) {
        return json_encode(implode('', $request->getQueryParams()));
    }
}