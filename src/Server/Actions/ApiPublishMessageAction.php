<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Server\Actions;

use MessageBroker\Server\Action;
use MessageBroker\Server\ActionException;
use MessageBroker\Server\JsonResponse;
use MessageBroker\Server\Request;
use MessageBroker\Store\DiskStore;

class ApiPublishMessageAction implements Action
{
    private const DEFAULT_MESSAGE_TTL = 300;

    public function __invoke(Request $request): JsonResponse
    {
        if (!isset($request->query['channel'])) {
            throw new ActionException('Query is missing a channel name');
        }
        $ttl = isset($request->query['ttl']) ? (int) $request->query['ttl'] : self::DEFAULT_MESSAGE_TTL;
        $messageId = (new DiskStore())->publish(
            $request->query['channel'],
            $request->getPayload(),
            $ttl
        );
        return new JsonResponse(
            [
                'messageId' => $messageId,
                'channel' => $request->query['channel'],
                'ttl' => $ttl
            ],
            JsonResponse::CODE_ACCEPTED
        );
    }
}