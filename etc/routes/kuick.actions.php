<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

//use Kuick\Example\UI\HelloAction;
use Kuick\Ops\Security\OpsGuard;
use Kuick\Ops\UI\OpsAction;
//use Kuick\Http\Request;

return [
    // //you probably want to remove this sample homepage
    // [
    //     'pattern' => '/',
    //     //'method' => Request::METHOD_GET, #optional, GET by default
    //     'action' => HelloAction::class,
    // ],
    //this route is protected by Bearer Token (see the configuration file)
    [
        'pattern' => '/api/ops',
        'action' => OpsAction::class,
        'guards' => [OpsGuard::class]
    ],
];
