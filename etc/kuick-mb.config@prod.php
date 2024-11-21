<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

return [
    //secure token for prod environment
    'kuick_ops_guards_token' => 'secure-token',

    //it allows user@pass (bearer token) to access "sample-channel" channel
    'message_broker_channel_tokens' => [
        'sample-channel' => [
            'user@better-password-but-change-it-anyway',
        ],
    ],
];