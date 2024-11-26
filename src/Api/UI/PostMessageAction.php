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
use Kuick\Http\ResponseCodes;
use Kuick\MessageBroker\Infrastructure\MessageStore\StoreInterface;
use Kuick\UI\ActionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class PostMessageAction implements ActionInterface
{
    private const DEFAULT_MESSAGE_TTL = 300;

    public function __construct(private StoreInterface $store, private LoggerInterface $logger)
    {
    }

    public function __invoke(ServerRequestInterface $request): JsonResponse
    {
        $ttl = $request->getQueryParams()['ttl'] ?? self::DEFAULT_MESSAGE_TTL;
        $messageId = $this->store->publish($request->getQueryParams()['channel'], $request->getBody()->getContents(), (int) $ttl > 0 ? $ttl : self::DEFAULT_MESSAGE_TTL);
        $this->logger->info('Published message: ' . $messageId);
        return new JsonResponse(
            [
                'messageId' => $messageId
            ],
            ResponseCodes::CREATED
        );
    }
}
