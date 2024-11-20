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

class ApiPublishMessageAction implements ActionInterface
{
    private const DEFAULT_MESSAGE_TTL = 300;

    public function __invoke(Request $request): JsonResponse
    {
        $ttl = (int) $request->getQueryParam('ttl') > 0 ? (int) $request->getQueryParam('ttl') : self::DEFAULT_MESSAGE_TTL;
        $messageId = (new DiskStore())->publish(
            $request->getQueryParam('channel'),
            $request->getBody(),
            $ttl
        );
        return new JsonResponse(
            [
                'messageId' => $messageId,
                'channel' => $request->getQueryParam('channel'),
                'ttl' => $ttl
            ],
            JsonResponse::CODE_ACCEPTED
        );
    }
}