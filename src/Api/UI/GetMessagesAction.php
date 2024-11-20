<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

 namespace Kuick\MessageBroker\Api\UI;

use Kuick\MessageBroker\Api\Security\TokenGuard;
use Kuick\UI\ActionInterface;
use Kuick\MessageBroker\Infrastructure\DiskStore;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GetMessagesAction implements ActionInterface
{
    public function __invoke(Request $request): JsonResponse
    {
        $userLabel = md5($request->headers->get(TokenGuard::TOKEN_HEADER));
        return new JsonResponse(
            (new DiskStore())->getMessages(
                $userLabel,
                $request->query->get('channel')
            )
        );
    }
}
