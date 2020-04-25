<?php

declare(strict_types=1);

namespace App\Middleware\Api;

use App\Middleware\IMiddleware;
use App\Middleware\AbsRequestMiddleware;
use Psr\Http\Message\ServerRequestInterface;

class ApiGetRequestMiddleware extends AbsRequestMiddleware implements IMiddleware{
    protected function middlewareAction (ServerRequestInterface $request) { 
        return $request->withQueryParams(
            ['json' => json_encode($request->getQueryParams())]
        );
    }
}