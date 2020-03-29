<?php
declare(strict_types=1);

namespace App\Controller;
use Psr\Http\Message\ServerRequestInterface;

interface IController {
    public function execute(ServerRequestInterface $request):void;
}