<?php
declare(strict_types=1);

namespace App\Middleware\Html;

use Psr\Http\Message\ResponseInterface;
use App\Middleware\IMiddleware;
use App\Middleware\AbsResponseMiddleware;
use App\Helper\ResponseOutputHelper;

class ResponseOutputMiddleware extends AbsResponseMiddleware implements IMiddleware{
    protected function middlewareAction (ResponseInterface $response) {
        ResponseOutputHelper::printResponse($response);
        return $response;
    }
}