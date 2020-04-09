<?php
declare(strict_types=1);

namespace App\Controller\Views;

use Psr\Http\Message\ServerRequestInterface;
use App\Controller\AbsController;
use App\Controller\ViewController;
use App\Helper\ViewControllerDependencies;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Auth\NeedsAuthMiddleware;


class PhotoManager extends ViewController implements \App\Controller\IController {
    use \App\Controller\Traits\GetMessageTrait;

    public function __construct(
        ViewControllerDependencies $view,
        NeedsAuthMiddleware $needsAuth
    ) {
        $template = 'photomanager';
        $middlewares = [
            new InjectableMiddleware($needsAuth)
        ];
        parent::__construct($template, $view, $middlewares);
    }

    protected function setViewParams($request) :array{
        $this->getResultMessage($request);
        return [
            'message' => $this->message,
            'msgStyle' => $this->msgStyle
        ];
    }
}