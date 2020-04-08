<?php
declare(strict_types=1);

namespace App\Controller\Views;

use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;
use App\Controller\ViewController;
use App\Helper\ResponseFactory;
use App\Middleware\Auth\NeedsAuthMiddleware;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Html\ResponseOutputMiddleware;

class Dashboard extends ViewController implements \App\Controller\IController {
    
    public function __construct(
        Engine $plates,
        ResponseFactory $responsefactory,
        ResponseOutputMiddleware $reponseOutput,
        // other middlewares
        NeedsAuthMiddleware $needsAuth
    ) {
        $template = 'dashboard';
        $middlewares = [
            new InjectableMiddleware($needsAuth)
        ];
        parent::__construct($template, $plates, $responsefactory, $reponseOutput, $middlewares);
    }

    protected function setViewParams($request) :array{
        return [];
    }
}