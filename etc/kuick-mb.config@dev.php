<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

return [
    //no token for dev environment
    'kuick_ops_guards_token' => '',

    //it allows user@pass (bearer token) to access "sample-channel" channel
    'kuick_mb_channel_tokens' => [
        'sample-channel' => [
            'user@pass',
        ],
    ],
];