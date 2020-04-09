<?php
declare(strict_types=1);

namespace App\Controller\Access;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Controller\AbsController;
use App\Api\Login\ValidateLoginApi;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Api\ApiPostRequestMiddleware;
use App\Middleware\Api\Api2HtmlResponseMiddleware;


class ValidateLogin extends AbsController implements \App\Controller\IController {
    use \App\Controller\Traits\SetMessageTrait;

    protected $apiAction;

    public function __construct(
        ValidateLoginApi $apiAction
    ) {
        $this->createUser = $apiAction;

        $middlewares = [];

        parent::__construct($middlewares);
    }

    protected function controllerResponse($request) {
        return $request;
    }
}