<?php

return [
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