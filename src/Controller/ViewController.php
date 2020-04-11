<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Controller\AbsController;
use App\Middleware\InjectableMiddleware;
use App\Helper\ViewControllerDependencies;

abstract class ViewController extends AbsController implements \App\Controller\IController {   
    use \App\Traits\GetUserTrait;

    protected $view;
    protected $template;

    public function __construct(
        string $template,
        ViewControllerDependencies $view,
        array $middlewares=[]
    ) {
        $this->template = $template;
        $this->view = $view;

        // always add a response output middleware as the last one
        $middlewares[] = new InjectableMiddleware($view->getOutputMiddleware());
        parent::__construct($middlewares);
    }

    protected abstract function setViewParams($request) :array;

    protected function controllerResponse($request) {
        $params = $this->setViewParams($request);
        $params['user'] = $this->findUser($request);
        
        return $this->view->setResponse($this->template, $params);
    }
}