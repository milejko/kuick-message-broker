<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Api\Security;

use Kuick\Http\BadRequestException;
use Kuick\Security\GuardInterface;
use Psr\Http\Message\ServerRequestInterface;

class MessageIdParamGuard implements GuardInterface
{
    public function __invoke(ServerRequestInterface $request): void
    {
        if (!isset($request->getQueryParams()['messageId'])) {
            throw new BadRequestException('Query param "messageId" is required');
        }
    }
}
