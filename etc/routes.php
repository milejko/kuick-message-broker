<?php

use MessageBroker\Server\Actions\ApiAckMessageAction;
use MessageBroker\Server\Actions\ApiGetMessageAction;
use MessageBroker\Server\Actions\ApiGetMessagesAction;
use MessageBroker\Server\Actions\ApiPublishMessageAction;
use MessageBroker\Server\Actions\RootAction;
use MessageBroker\Server\Filters\ChannelFilter;
use MessageBroker\Server\Filters\MessageIdFilter;
use MessageBroker\Server\Guards\ApiGuard;
use MessageBroker\Server\Request;

return [
    [
        'method' => Request::METHOD_GET,
        'path' => '/',
        'action' => RootAction::class,
        'guards' => [],
        'filters' => [],
    ],
    [
        'method' => Request::METHOD_GET,
        'path' => '/api/messages',
        'action' => ApiGetMessagesAction::class,
        'guards' => [ApiGuard::class],
        'filters' => [ChannelFilter::class]
    ],
    [
        'method' => Request::METHOD_GET,
        'path' => '/api/message',
        'action' => ApiGetMessageAction::class,
        'guards' => [ApiGuard::class],
        'filters' => [ChannelFilter::class, MessageIdFilter::class]
    ],
    [
        'method' => Request::METHOD_POST,
        'path' => '/api/message',
        'action' => ApiPublishMessageAction::class,
        'guards' => [ApiGuard::class],
        'filters' => [ChannelFilter::class]
    ],
    [
        'method' => Request::METHOD_PUT,
        'path' => '/api/message/ack',
        'action' => ApiAckMessageAction::class,
        'guards' => [ApiGuard::class],
        'filters' => [ChannelFilter::class, MessageIdFilter::class]
    ],
];
