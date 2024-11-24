<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    //no token for dev
    'kuick.app.ops.guards.token' => '',
    //debug for dev
    'kuick.app.monolog.level' => 'DEBUG',
    
    //different handlers
    'kuick.app.monolog.handlers' => [
        [
            'type' => 'stream',
            'path' => 'php://stdout',
            'level' => 'DEBUG',
        ],
        // [
        //     'type' => 'stream',
        //     'path' => BASE_PATH . '/var/tmp/custom-log.log',
        //     'level' => 'INFO',
        // ],
    ],
];