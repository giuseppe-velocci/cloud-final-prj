<?php

declare(strict_types=1);

namespace App\Middleware\Api;
use App\Middleware\IMiddleware;

use Psr\Http\Message\ServerRequestInterface;

class ApiPostRequestMiddleware extends AbsApiRequestMiddleware implements IMiddleware{
    protected function middlewareAction (ServerRequestInterface $request) {
        return $request->withParsedBody(
            ['json' => json_encode($request->getParsedBody())]
        );
    }
}