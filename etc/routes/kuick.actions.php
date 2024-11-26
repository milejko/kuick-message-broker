<?php

use Kuick\Example\UI\HelloAction;
use Kuick\Ops\Security\OpsGuard;
use Kuick\Ops\UI\OpsAction;

return [
    [
        'pattern' => '/hello',
        'action' => HelloAction::class,
    ],
    [
        'pattern' => '/api/ops',
        'action' => OpsAction::class,
        'guards' => [OpsGuard::class]
    ],
];
