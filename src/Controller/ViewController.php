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

    /**
     * Returns arry with view params to be passed to the view. 
     * May throw \Exception if something goes wrong. 
     * The thrown exception MUST return an error code == http status code
     * An error page with name that matches the status code error MUST exist
     */
    protected abstract function setViewParams($request) :array;

    /**
     * Main response for the ViewController. Runs inside execute() method
     */
    protected function controllerResponse($request) {
        try {
            $params = $this->setViewParams($request);

        } catch (\Exception $e) {
            return $this->view->setResponse(
                sprintf('Errors/%d', (int) $e->getCode()), []
            );
        }
        
        $params['user'] = $this->findUser($request);
        
        return $this->view->setResponse($this->template, $params);
    }
}