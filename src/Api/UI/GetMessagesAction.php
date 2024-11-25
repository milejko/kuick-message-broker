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
use Kuick\MessageBroker\Infrastructure\MessageStore\StoreInterface;
use Kuick\UI\ActionInterface;
use Psr\Log\LoggerInterface;

class GetMessagesAction implements ActionInterface
{
    public function __construct(private StoreInterface $store, private LoggerInterface $logger)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $userToken = $request->headers->get(TokenGuard::TOKEN_HEADER);
        $messages = $this->store->getMessages(
            $request->query->get('channel'),
            $userToken,
        );
        $this->logger->info('Listing messages for user: ' . md5($userToken) . ' list contains: ' . count($messages) . ' messages');
        return new JsonResponse($messages);
    }
}
