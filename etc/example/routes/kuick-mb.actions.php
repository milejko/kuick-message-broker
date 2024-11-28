<?php

use KuickMessageBroker\Api\Security\MessageIdParamGuard;
use KuickMessageBroker\Api\Security\TokenGuard;
use KuickMessageBroker\Api\UI\GetMessageAction;
use KuickMessageBroker\Api\UI\GetMessagesAction;
use KuickMessageBroker\Api\UI\HomeAction;
use KuickMessageBroker\Api\UI\PostMessageAckAction;
use KuickMessageBroker\Api\UI\PostMessageAction;

return [
    //remove this route if you need to specify your own "home" route
    [
        'pattern' => '/',
        'action' => HomeAction::class
    ],
    [
        'pattern' => '/api/message',
        'action' => GetMessageAction::class,
        'guards' => [TokenGuard::class, MessageIdParamGuard::class]
    ],
    [
        'pattern' => '/api/messages',
        'action' => GetMessagesAction::class,
        'guards' => [TokenGuard::class],
    ],
    [
        'method' => 'POST',
        'pattern' => '/api/message',
        'action' => PostMessageAction::class,
        'guards' => [TokenGuard::class],
    ],
    [
        'method' => 'POST',
        'pattern' => '/api/message/ack',
        'action' => PostMessageAckAction::class,
        'guards' => [TokenGuard::class, MessageIdParamGuard::class],
    ],
];