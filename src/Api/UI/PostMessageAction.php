<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Api\UI;

use Kuick\UI\ActionInterface;
use Kuick\MessageBroker\Infrastructure\DiskStore;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PostMessageAction implements ActionInterface
{
    private const DEFAULT_MESSAGE_TTL = 300;

    public function __invoke(Request $request): JsonResponse
    {
        $ttl = (int) $request->query->get('ttl') > 0 ? (int) $request->query->get('ttl') : self::DEFAULT_MESSAGE_TTL;
        $channel = $request->query->get('channel');

        $messageId = (new DiskStore())->publish(
            $channel,
            $request->getContent(),
            $ttl
        );
        return new JsonResponse(
            [
                'messageId' => $messageId,
                'channel' => $channel,
                'ttl' => $ttl,
            ],
            JsonResponse::HTTP_ACCEPTED
        );
    }
}