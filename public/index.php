<?php

use MessageBroker\Server\Kernel;

define('BASE_PATH', __DIR__ . '/../');
require BASE_PATH . '/vendor/autoload.php';

(new Kernel())
    ->setGlobalContext(
        getenv(),
        $_SERVER,
        file_get_contents('php://input')
    )
    ->run();
