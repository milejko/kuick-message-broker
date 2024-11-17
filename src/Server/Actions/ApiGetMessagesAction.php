<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Server\Actions;

use MessageBroker\Server\Action;
use MessageBroker\Server\JsonResponse;
use MessageBroker\Server\Request;

/**
 * 
 */
class ApiGetMessagesAction implements Action
{
    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse(['ya messages' => [':)']]);
    }
}