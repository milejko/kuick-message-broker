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
use MessageBroker\Server\JsonNotFoundResponse;
use MessageBroker\Server\JsonResponse;
use MessageBroker\Server\Request;
use MessageBroker\Store\DiskStore;
use MessageBroker\Store\NotFoundException;

class ApiAckMessageAction implements Action
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            (new DiskStore())->ack(
                $request->getHeader('X-User-Token'),
                $request->getQueryParam('channel'),
                $request->getQueryParam('messageId')
            );
        } catch (NotFoundException $error) {
            return new JsonNotFoundResponse($error);
        }
        return new JsonResponse(
            [
                'success' => true,
                'channel' => $request->getQueryParam('channel'),
                'messageId' => $request->getQueryParam('messageId')
            ],
            JsonResponse::CODE_ACCEPTED
        );
    }
}