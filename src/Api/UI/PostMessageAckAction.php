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
use Kuick\Http\NotFoundException;
use Kuick\Http\Request;
use Kuick\MessageBroker\Api\Security\TokenGuard;
use Kuick\MessageBroker\Infrastructure\MessageStore\MessageNotFoundException;
use Kuick\MessageBroker\Infrastructure\MessageStore\StoreInterface;
use Kuick\UI\ActionInterface;
use Psr\Log\LoggerInterface;

class PostMessageAckAction implements ActionInterface
{
    public function __construct(private StoreInterface $store, private LoggerInterface $logger)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $channel = $request->query->get('channel');
        $messageId = $request->query->get('messageId');
        $userToken = $request->headers->get(TokenGuard::TOKEN_HEADER);
        try {
            $this->store->ack(
                $channel,
                $messageId,
                $userToken,
            );
            $this->logger->info('Acked message: ' . $messageId . ' by user: ' . md5($userToken));
        } catch (MessageNotFoundException $error) {
            throw new NotFoundException($error->getMessage());
        }
        return new JsonResponse(
            [
                'messageId' => $messageId,
                'acked' => true,
            ],
            JsonResponse::HTTP_ACCEPTED
        );
    }
}
