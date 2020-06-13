<?php
declare(strict_types=1);

namespace App\Controller\Access;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Api\Login\LoginApi;
use App\Api\Upload\RefreshImagesApi;
use App\Controller\AbsController;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Api\ApiPostRequestMiddleware;
use App\Middleware\Api\Api2HtmlResponseMiddleware;
use App\Middleware\Cookie\SetCookieMiddleware;
use App\Middleware\Html\ResponseOutputMiddleware;


class Login extends AbsController implements \App\Controller\IController {
    use \App\Traits\SetMessageTrait;
//    use \App\Traits\SetCookieTrait;

    protected $refreshApi;
    protected $apiAction;
    protected $request;

    public function __construct(
        LoginApi $apiAction, 
        RefreshImagesApi $refreshApi,
        ApiPostRequestMiddleware $apiRequestMiddleware,
        Api2HtmlResponseMiddleware $apiResponseMiddleware,
        SetCookieMiddleware $cookieMiddleware,
        ResponseOutputMiddleware $outputMiddleware
    ) {
        $this->apiAction = $apiAction;
        $this->refreshApi = $refreshApi;

        $middlewares = [
            new InjectableMiddleware($apiRequestMiddleware),
            new InjectableMiddleware($apiResponseMiddleware),
            new InjectableMiddleware($cookieMiddleware),
            new InjectableMiddleware($outputMiddleware,
                function($response) {
                    $this->handleResponse($response);
                }
            ),
        ];

        parent::__construct($middlewares);
    }


    protected function controllerResponse($request) {
        $this->request = $request; // store currente request for later use
        return $this->apiAction->execute($request);
    }

    /**
     * refresh SAS request
     */
    protected function refreshSasUrls() {

        $this->refreshApi->execute($this->request);
    }


    protected function handleResponse($response) {
     if ($response->getStatusCode() != 200) {
            $this->setResultMessage($response);
            $landing = 'login';
           
        } else {
           $landing = 'dashboard';

            // refresh imagesSAS (if needed)
            $this->refreshSasUrls();

        }

        header(sprintf('Location: /%s', $landing));
        exit;
    }
}