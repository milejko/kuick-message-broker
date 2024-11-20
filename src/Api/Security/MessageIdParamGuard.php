<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Api\Security;

use Kuick\Security\GuardInterface;
use Kuick\UI\UIBadRequestException;
use Symfony\Component\HttpFoundation\Request;

class MessageIdParamGuard implements GuardInterface
{
    public function __invoke(Request $request): void
    {
        if (!$request->query->get('messageId')) {
            throw new UIBadRequestException('Query param "messageId" is required');
        }
    }
}
