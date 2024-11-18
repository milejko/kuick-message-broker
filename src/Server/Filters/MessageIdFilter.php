<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Server\Filters;

use MessageBroker\Server\Filter;
use MessageBroker\Server\FilterException;
use MessageBroker\Server\Request;


class MessageIdFilter implements Filter
{
    public function __invoke(Request $request): void
    {
        if (!$request->getQueryParam('messageId')) {
            throw new FilterException('Query param "messageId" is required');
        }
    }
}