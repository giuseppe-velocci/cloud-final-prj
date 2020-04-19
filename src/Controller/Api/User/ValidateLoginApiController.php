<?php
declare(strict_types=1);

namespace App\Controller\Api\User;

use Psr\Http\Message\ServerRequestInterface;
use App\Api\Login\ValidateLoginApi;
use App\Helper\ResponseOutputHelper;

class ValidateLoginApiController extends ApiController implements \App\Controller\IController {
    public function __construct(
        ValidateLoginApi $apiAction
    ) {
        $this->apiAction = $apiAction;
    }
}