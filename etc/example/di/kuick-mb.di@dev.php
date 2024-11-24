<?php

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    //some config and injections only for dev
    //dummy tokens
    'kuick.mb.consumer.tokens' => [
        'sample-channel' => [
            'user@pass',
            'another-user@pass2',
        ],
    ],
    'kuick.mb.publisher.tokens' => [
        'sample-channel' => [
            'user@pass',
            //another-user can not publish to "sample-channel"
            //'another-user@pass2',
        ],
    ],
];