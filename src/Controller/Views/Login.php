<?php
declare(strict_types=1);

namespace App\Controller\Views;

use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;
use App\Helper\ResponseFactory;
use App\Controller\ViewController;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Html\ResponseOutputMiddleware;

class Login extends ViewController implements \App\Controller\IController {
    use \App\Controller\Traits\GetMessageTrait;

    public function __construct(
        Engine $plates,
        ResponseFactory $responsefactory,
        ResponseOutputMiddleware $reponseOutput
    ) {
        $template = 'login';
        $middlewares = [];
        parent::__construct($template, $plates, $responsefactory, $reponseOutput, $middlewares);

    }

    protected function setViewParams($request) :array{
        $this->getResultMessage($request);
        return [
            'message' => $this->message,
            'msgStyle' => $this->msgStyle
        ];
    }
}