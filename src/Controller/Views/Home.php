<?php
declare(strict_types=1);

namespace App\Controller\Views;
use Psr\Http\Message\ServerRequestInterface;
use League\Plates\Engine;

class Home implements \App\Controller\IController {
    protected $plates;

    public function __construct(Engine $plates)
    {
        $this->plates  = $plates;
    }


    public function execute(ServerRequestInterface $request) :void {
        echo $this->plates->render('home', [
           
        ]);
    }
}