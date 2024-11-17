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

/**
 * 
 */
class ApiGuard implements Guard
{
    private const DEFAULT_OPERATOR_TOKEN = 'admin@xUjJhsf5ty7OpLL';

    public function __invoke(Request $request): void
    {
        if ('' == $request->getHeader('x-user-token')) {
            throw new GuardException('X-User-Token missing');
        }
        if (self::DEFAULT_OPERATOR_TOKEN != $request->getHeader('x-user-token')) {
            throw new GuardException('X-User-Token invalid');
        }
    }
}