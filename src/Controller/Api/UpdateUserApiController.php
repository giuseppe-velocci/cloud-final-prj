<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Psr\Http\Message\ServerRequestInterface;
use App\Api\Login\UpdateUserApi;
use App\Helper\ResponseOutputHelper;

class UpdateUserApiController extends ApiController implements \App\Controller\IController {
    public function __construct(
        UpdateUserApi $apiAction
    ) {
        $this->apiAction = $apiAction;
    }
}