<?php
declare(strict_types=1);

namespace App\Middleware;
use Psr\Http\Message\ServerRequestInterface;

interface IHttpRequestMiddleware {
    public function handle(ServerRequestInterface $request, callable $next);
}