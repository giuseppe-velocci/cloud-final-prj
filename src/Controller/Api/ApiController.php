<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Psr\Http\Message\ServerRequestInterface;
use App\Api\AbsApi;
use App\Controller\AbsController;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Html\ResponseOutputMiddleware;

class ApiController extends AbsController implements \App\Controller\IController {
    protected $apiAction;

    public function __construct(
        AbsApi $apiAction,
        ResponseOutputMiddleware $reponseOutput
    ) {
        $this->apiAction = $apiAction;
        $middlewares = [
            new InjectableMiddleware($reponseOutput)
        ];
        parent::__construct($middlewares);
    }

    protected function controllerResponse($request) {
        return $this->apiAction->execute($request);
    }
}