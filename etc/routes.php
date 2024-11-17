<?php

use MessageBroker\Server\Actions\ApiAckAction;
use MessageBroker\Server\Actions\ApiAckMessageAction;
use MessageBroker\Server\Actions\ApiGetMessagesAction;
use MessageBroker\Server\Actions\ApiPublishMessageAction;
use MessageBroker\Server\Actions\DefaultAction;
use MessageBroker\Server\Guards\ApiGuard;

return [
    [
        "method" => "GET",
        "path" => "/",
        "action" => DefaultAction::class,
    ],
    [
        "method" => "GET",
        "path" => "/api/messages",
        "action" => ApiGetMessagesAction::class,
        "guard" => ApiGuard::class,
    ],
    [
        "method" => "POST",
        "path" => "/api/message",
        "action" => ApiPublishMessageAction::class,
        "guard" => ApiGuard::class,
    ],
    [
        "method" => "PUT",
        "path" => "/api/message/ack",
        "action" => ApiAckMessageAction::class,
        "guard" => ApiGuard::class,
    ],
];
