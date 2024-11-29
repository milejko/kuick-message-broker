<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace KuickMessageBroker\Api\UI;

use Kuick\Http\JsonResponse;
use KuickMessageBroker\Api\Security\TokenGuard;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class GetMessagesController
{
    public function __construct(private MessageStore $store, private LoggerInterface $logger)
    {
    }

    public function __invoke(string $channel, ServerRequestInterface $request): JsonResponse
    {
        $userToken = $request->getHeaderLine(TokenGuard::TOKEN_HEADER);
        $messages = $this->store->getMessages(
            $channel,
            $userToken,
        );
        $this->logger->info('Listing messages for user: ' . md5($userToken) . ' list contains: ' . count($messages) . ' messages');
        return new JsonResponse($messages);
    }
}
