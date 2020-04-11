<?php
declare(strict_types=1);

namespace App\Controller\Actions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Helper\HashMsg;
use App\Api\Upload\UploadFileApi;
use App\Middleware\Api\ApiPostRequestMiddleware;
use App\Middleware\Api\Api2HtmlResponseMiddleware;
use App\Middleware\Auth\NeedsAuthMiddleware;
use App\Controller\AbsController;
use App\Middleware\InjectableMiddleware;

class UploadFileAction extends AbsController implements \App\Controller\IController {
    use \App\Traits\SetMessageTrait;
    use \App\Traits\GetCookieTrait;

    protected $apiAction;

    public function __construct(
        UploadFileApi $apiAction, 
        NeedsAuthMiddleware $auth,
        ApiPostRequestMiddleware $apiRequestMiddleware,
        Api2HtmlResponseMiddleware $apiResponseMiddleware
    ) {
        $this->apiAction = $apiAction;

        $middlewares = [
            new InjectableMiddleware($auth),
            new InjectableMiddleware($apiRequestMiddleware,
                function($request) {
                    return $this->handleRequest($request);
                }
            ),
            new InjectableMiddleware($apiResponseMiddleware,
                function($response) {
                    $this->handleResponse($response);
                }
            ),
        ];

        parent::__construct($middlewares);
    }


    protected function controllerResponse($request) {
        return $this->apiAction->execute($request);
    }

    /**
     * adds fields for user to current request body (needed to store userId on Image collection)
     */
    protected function handleRequest($request) {
        $cookies = $this->getCookies($request);
        $postParams = $request->getParsedBody();
        $postParams['user'] = $cookies['user'];

        return $request->withParsedBody($postParams);
    }


    protected function handleResponse($response) {
        $landing = 'photomanager';
        $this->setResultMessage($response);
        
        header(sprintf('Location: /%s', $landing));
        exit;
    }
}