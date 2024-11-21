<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use Kuick\Example\UI\Console\HelloCommand;

return [
    //you probably want to remove this one
    [
        'name' => 'hello',
        'command' => HelloCommand::class,
        //'description' => 'Optional description'
    ],
];