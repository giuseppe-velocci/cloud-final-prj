<?php

declare(strict_types=1);

namespace App\Middleware\Api;

use App\Middleware\IMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use App\Middleware\AbsRequestMiddleware;

class ApiGetRequestMiddleware extends AbsRequestMiddleware implements IMiddleware{
    protected function middlewareAction (ServerRequestInterface $request) {
        return json_encode(implode('', $request->getQueryParams()));
    }
}