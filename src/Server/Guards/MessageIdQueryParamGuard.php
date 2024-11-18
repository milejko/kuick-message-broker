<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Server\Guards;

use MessageBroker\Server\Guard;
use MessageBroker\Server\GuardException;
use MessageBroker\Server\Request;

class MessageIdQueryParamGuard implements Guard
{
    public function __invoke(Request $request): void
    {
        if (!$request->getQueryParam('messageId')) {
            throw new GuardException('Query param "messageId" is required');
        }
    }
}