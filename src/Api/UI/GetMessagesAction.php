<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

 namespace Kuick\MessageBroker\Api\UI;

use Kuick\Http\JsonResponse;
use Kuick\Http\Request;
use Kuick\UI\ActionInterface;
use Kuick\MessageBroker\Infrastructure\DiskStore;

class GetMessagesAction implements ActionInterface
{
    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse(
            (new DiskStore())->getMessages(
                $request->getHeader('x-user-token'),
                $request->getQueryParam('channel')
            )
        );
    }
}
