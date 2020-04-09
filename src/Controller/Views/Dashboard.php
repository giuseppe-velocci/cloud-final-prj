<?php
declare(strict_types=1);

namespace App\Controller\Views;

use Psr\Http\Message\ServerRequestInterface;
use App\Controller\AbsController;
use App\Controller\ViewController;
use App\Middleware\InjectableMiddleware;
use App\Helper\ViewControllerDependencies;
use App\Middleware\Auth\NeedsAuthMiddleware;


class Dashboard extends ViewController implements \App\Controller\IController {
    
    public function __construct(
        ViewControllerDependencies $view,
        NeedsAuthMiddleware $needsAuth
    ) {
        $template = 'dashboard';
        $middlewares = [
            new InjectableMiddleware($needsAuth)
        ];
        parent::__construct($template, $view, $middlewares);
    }

    protected function setViewParams($request) :array{
        return [];
    }
}