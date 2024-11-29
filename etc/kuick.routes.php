<?php

use Kuick\Example\UI\PingController;
use Kuick\Ops\Security\OpsGuard;
use Kuick\Ops\UI\OpsController;

return [
    //named parameter see the controller how
    [
        'path' => '/hello/(?<name>[a-zA-Z0-9-]+)',
        'controller' => PingController::class,
    ],
    //this route is protected by Bearer Token (see ./di/kuick.di.php file, and the OpsGuard)
    [
        'path' => '/api/ops',
        'controller' => OpsController::class,
        'guards' => [OpsGuard::class]
    ],
];
