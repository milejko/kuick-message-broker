<?php

/**
 * Kuick Framework (https://github.com/milejko/kuick-framework)
 *
 * @link       https://github.com/milejko/kuick-framework
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use Kuick\MessageBroker\Api\Security\TokenGuard;

return [
    'app_charset'   => 'UTF-8',
    'app_locale'    => 'en_US.utf-8',
    'app_timezone'  => 'Europe/London',
    'app_env'       => 'prod',

    TokenGuard::CHANNEL_TOKEN_CONFIG_KEY => [
        'cms' => [
            'user1@h59vXXa1pdh2',
            'user2@104hGFWodajs'
        ],
    ],
];