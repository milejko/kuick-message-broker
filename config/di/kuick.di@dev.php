<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use function DI\env;

return [
    //simple token for dev
    'kuick.ops.guard.token' => env('KUICK_OPS_GUARD_TOKEN', 'let-me-in'),

    //debug for dev
    'kuick.app.monolog.level' => env('KUICK_APP_MONOLOG_LEVEL', 'DEBUG'),
    'kuick.app.monolog.usemicroseconds' => env('KUICK_APP_MONOLOG_USEMICROSECONDS', true),
];