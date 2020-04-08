<?php
declare(strict_types=1);

namespace App\Controller\Errors;

use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;
use App\Helper\ResponseFactory;
use App\Controller\ViewController;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Html\ResponseOutputMiddleware;

class Error404 extends ViewController implements \App\Controller\IController {
    public function __construct(
        Engine $plates,
        ResponseFactory $responsefactory,
        ResponseOutputMiddleware $reponseOutput
    ) {
        $template = 'Errors/404';
        $middlewares = [];
        parent::__construct($template, $plates, $responsefactory, $reponseOutput, $middlewares);

    }


    protected function setViewParams($request) :array{
        return [];
    }
}