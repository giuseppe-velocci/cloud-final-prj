<?php
declare(strict_types=1);

namespace App\Controller\Errors;

use Psr\Http\Message\ServerRequestInterface;
use App\Controller\AbsController;
use App\Controller\ViewController;
use App\Helper\ViewControllerDependencies;
use App\Middleware\InjectableMiddleware;

class Error404 extends ViewController implements \App\Controller\IController {
    public function __construct(
        ViewControllerDependencies $view
    ) {
        $template = 'Errors/404';
        $middlewares = [];
        parent::__construct($template, $view, $middlewares);
    }


    protected function setViewParams($request) :array{
        return [];
    }
}