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
use Kuick\MessageBroker\Api\Security\TokenGuard;
use Kuick\UI\ActionInterface;
use Kuick\MessageBroker\Infrastructure\DiskStore;
use Kuick\MessageBroker\Infrastructure\StoreInterface;

class GetMessagesAction implements ActionInterface
{
    public function __construct(private StoreInterface $store)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $userLabel = md5($request->headers->get(TokenGuard::TOKEN_HEADER));
        return new JsonResponse(
            $this->store->getMessages(
                $userLabel,
                $request->query->get('channel')
            )
        );
    }
}
