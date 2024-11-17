<?php

use MessageBroker\Server\Actions\ApiGetMessagesAction;
use MessageBroker\Server\Actions\ApiPublishAction;
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
        "action" => ApiPublishAction::class,
        "guard" => ApiGuard::class,
    ],
    [
        "method" => "PUT",
        "path" => "/api/message",
        "action" => ApiPublishAction::class,
        "guard" => ApiGuard::class,
    ],
];
