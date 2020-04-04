<?php

use League\Plates\Engine;
use Psr\Container\ContainerInterface;
use Zend\Diactoros\ResponseFactory;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\WriteConcern;
//
use App\Db\User;
use App\Db\MongoConnection;

use App\Helper\ISanitizer;
use App\Helper\CryptMsg;

use App\Controller\Access\Register;



return [
    'view_path' => 'templates',
    
    Engine::class => function(ContainerInterface $c) {
        return new Engine($c->get('view_path'));
    },

    MongoConnection::class => function(ContainerInterface $c) {
        return new MongoConnection();
    },

    BulkWrite::class => function (ContainerInterface $c) {
        return new BulkWrite();
    },

    WriteConcern::class => function (ContainerInterface $c) {
        return new WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY);
    },

    ISanitizer::class => function (ContainerInterface $c) {
        return new App\Helper\Sanitizer();
    },

    // encrypting
    CryptMsg::class => function(ContainerInterface $c) {
        return CryptMsg::instance();
    },
];