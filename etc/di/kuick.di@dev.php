<?php

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    //debug for dev
    'kuick.app.monolog.level' => 'DEBUG',
    'kuick.app.monolog.useMicroseconds' => true,
    //different handlers
    'kuick.app.monolog.handlers' => [
        [
            'type' => 'stream',
            'path' => 'php://stdout',
        ]
    ],

    //no token for dev
    'kuick.app.ops.guards.token' => '',
];