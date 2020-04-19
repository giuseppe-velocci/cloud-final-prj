<?php
declare(strict_types=1);

namespace App\Controller\Api\User;

use Psr\Http\Message\ServerRequestInterface;
use App\Api\Login\LoginApi;
use App\Helper\ResponseOutputHelper;

class LoginApiController extends ApiController implements \App\Controller\IController {
    public function __construct(
        LoginApi $apiAction
    ) {
        $this->apiAction = $apiAction;
    }
}