<?php

use Kuick\Example\UI\HelloAction;
use Kuick\Http\Request;
use Kuick\Ops\Security\OpsGuard;
use Kuick\Ops\UI\OpsAction;

return [
    //this route is protected by Bearer Token (see the configuration file)
    [
        'pattern' => '/api/ops',
        'action' => OpsAction::class,
        'guards' => [OpsGuard::class]
    ],
];
