<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Api\Security;

use Kuick\Http\HttpForbiddenException;
use Kuick\Http\Request;
use Kuick\Security\GuardInterface;

class TokenGuard implements GuardInterface
{
    private const DEFAULT_OPERATOR_TOKEN = 'admin@xUjJhsf5ty7OpLL';

    public function __invoke(Request $request): void
    {
        if (self::DEFAULT_OPERATOR_TOKEN != $request->getHeader('x-user-token')) {
            throw new HttpForbiddenException('X-User-Token invalid');
        }
    }
}