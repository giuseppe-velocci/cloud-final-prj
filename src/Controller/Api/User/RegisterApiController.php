<?php
declare(strict_types=1);

namespace App\Controller\Api\User;

use Psr\Http\Message\ServerRequestInterface;
use App\Api\Registration\CreateUserApi;
use App\Helper\ResponseOutputHelper;

class RegisterApiController extends ApiController implements \App\Controller\IController {
    public function __construct(
        CreateUserApi $apiAction
    ) {
        $this->apiAction = $apiAction;
    }
}
