<?php
declare(strict_types=1);

namespace App\Controller\Views;

use Psr\Http\Message\ServerRequestInterface;
use App\Controller\AbsController;
use App\Controller\ViewController;
use App\Middleware\InjectableMiddleware;
use App\Helper\ViewControllerDependencies;

class Register extends ViewController implements \App\Controller\IController {
    use \App\Traits\GetMessageTrait;

    public function __construct(
        ViewControllerDependencies $view
    ) {
        $template = 'register';
        $middlewares = [];
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