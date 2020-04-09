<?php
declare(strict_types=1);

namespace App\Helper;

use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Helper\ResponseFactory;
use App\Middleware\Html\ResponseOutputMiddleware;

class ViewControllerDependencies {
    protected $plates;
    protected $responseFactory;
    protected $outputMiddleware;

    public function __construct(
        Engine $plates,
        ResponseFactory $responseFactory,
        ResponseOutputMiddleware $outputMiddleware
    ) {
        $this->plates = $plates;
        $this->responseFactory = $responseFactory;
        $this->outputMiddleware = $outputMiddleware;
    }

    public function setResponse(string $template, array $params=[], int $statusCode=200) {
        return $this->responseFactory->createResponse(
            ResponseFactory::HTML,
            $statusCode,
            $this->plates->render(
                $template, 
                $params
            )
            );
    }

    public function getOutputMiddleware() {
        return clone $this->outputMiddleware;
    }
} 