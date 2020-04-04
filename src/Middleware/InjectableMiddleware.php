<?php
declare(strict_types=1);

namespace App\Middleware;
use App\Middleware\Interfaces\IMiddleware;

class InjectableMiddleware {
    protected $next;
    protected $middleware;

    public function __construct(
        $middleware,
        ?callable $next=null 
    ) {
        $this->middleware = $middleware;
        $this->next = $next;
    }

    public function handle($data) {
        return $this->middleware->handle($data, $this->next);
    }
}