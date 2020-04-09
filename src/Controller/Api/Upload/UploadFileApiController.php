<?php
declare(strict_types=1);

namespace App\Controller\Api\Upload;

use Psr\Http\Message\ServerRequestInterface;
use App\Api\Upload\UploadFileApi;
use App\Helper\ResponseOutputHelper;
use App\Middleware\Auth\ApiNeedsAuthMiddleware;
use App\Controller\Api\ApiController;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Html\ResponseOutputMiddleware;

class UploadFileApiController extends ApiController implements \App\Controller\IController {
    
    public function __construct(
        UploadFileApi $apiAction,
        ApiNeedsAuthMiddleware $apiAuth,
        ResponseOutputMiddleware $reponseOutput
    ) {
        $middlewares = [new InjectableMiddleware($apiAuth)];

        parent::__construct($apiAction, $reponseOutput, $middlewares);
    }
}