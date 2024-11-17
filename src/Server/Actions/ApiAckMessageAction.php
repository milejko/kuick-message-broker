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
use MessageBroker\Server\JsonErrorResponse;
use MessageBroker\Server\JsonResponse;
use MessageBroker\Server\Request;
use MessageBroker\Store\DiskStore;

class ApiAckMessageAction implements Action
{
    public function __invoke(Request $request): JsonResponse
    {
        if (!isset($request->query['channel'])) {
            throw new ActionException('Query is missing a channel name');
        }
        $isAcked = (new DiskStore())->ack(
            $request->getHeader('X-User-Token'),
            $request->query['channel'],
            $request->query['messageId']
        );
        if (!$isAcked) {
            return new JsonErrorResponse(
                'Message not found',
                JsonResponse::CODE_NOT_FOUND
            );
        }
        return new JsonResponse(
            [
                'status' => 'ok',
                'messageId' => $request->query['messageId'],
                'channel' => $request->query['channel'],
            ],
            JsonResponse::CODE_ACCEPTED
        );
    }
}