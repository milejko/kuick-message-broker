<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use Kuick\Http\RequestMethod;
use Kuick\MessageBroker\Api\Security\TokenGuard;
use Kuick\MessageBroker\Api\UI\GetMessageAction;
use Kuick\MessageBroker\Api\UI\GetMessagesAction;
use Kuick\MessageBroker\Api\UI\PostMessageAckAction;
use Kuick\MessageBroker\Api\UI\PostMessageAction;
use Kuick\MessageBroker\Shared\UI\HomeAction;

return [
    [
        'pattern' => '/',
        'action' => HomeAction::class
    ],
    [
        'pattern' => '/api/message',
        'action' => GetMessageAction::class
    ],
    [
        'pattern' => '/api/messages',
        'action' => GetMessagesAction::class,
        'guards' => [TokenGuard::class],
    ],
    [
        'method' => RequestMethod::POST,
        'pattern' => '/api/message',
        'action' => PostMessageAction::class,
        'guards' => [TokenGuard::class],
    ],
    [
        'method' => RequestMethod::POST,
        'pattern' => '/api/message/ack',
        'action' => PostMessageAckAction::class,
        'guards' => [TokenGuard::class],
    ],
];