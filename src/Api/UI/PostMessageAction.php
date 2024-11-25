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
use Kuick\MessageBroker\Infrastructure\MessageStore\StoreInterface;
use Kuick\UI\ActionInterface;
use Psr\Log\LoggerInterface;

class PostMessageAction implements ActionInterface
{
    private const DEFAULT_MESSAGE_TTL = 300;

    public function __construct(private StoreInterface $store, private LoggerInterface $logger)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $ttl = (int) $request->query->get('ttl') > 0 ? (int) $request->query->get('ttl') : self::DEFAULT_MESSAGE_TTL;
        $messageId = $this->store->publish($request->query->get('channel'), $request->getContent(), $ttl);
        $this->logger->info('Published message: ' . $messageId);
        return new JsonResponse(
            [
                'messageId' => $messageId
            ],
            JsonResponse::HTTP_ACCEPTED
        );
    }
}
