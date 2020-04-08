<?php
declare(strict_types=1);

namespace App\Controller;

use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Helper\ResponseFactory;
use App\Controller\AbsController;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Html\ResponseOutputMiddleware;

abstract class ViewController extends AbsController implements \App\Controller\IController {   
    use \App\Controller\Traits\GetUserTrait;

    protected $plates;
    protected $responsefactory;
    protected $template;
    protected $statusCode=200;

    public function __construct(
        string $template,
        Engine $plates,
        ResponseFactory $responsefactory,
        ResponseOutputMiddleware $reponseOutput,
        array $middlewares=[]
    ) {
        $this->template = $template;
        $this->plates = $plates;
        $this->responsefactory = $responsefactory;

        // always add a response output middleware as the last one
        $middlewares[] = new InjectableMiddleware($reponseOutput);
        parent::__construct($middlewares);
    }

    protected abstract function setViewParams($request) :array;

    protected function controllerResponse($request) {
        $params = $this->setViewParams($request);
        $params['user'] = $this->findUser($request);
        
        return $this->responsefactory->createResponse(
            ResponseFactory::HTML,
            $this->statusCode,
            $this->plates->render(
                $this->template, 
                $params
            )
        );
    }
}