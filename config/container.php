<?php

use League\Plates\Engine;
use Psr\Container\ContainerInterface;

return [
    'view_path' => 'templates',
    
    Engine::class => function(ContainerInterface $c) {
        return new Engine($c->get('view_path'));
    },

];
