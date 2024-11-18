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
use MessageBroker\Server\JsonBadRequestResponse;
use MessageBroker\Server\JsonNotFoundResponse;
use MessageBroker\Server\JsonResponse;
use MessageBroker\Server\Request;
use MessageBroker\Store\DiskStore;
use MessageBroker\Store\NotFoundException;
use MessageBroker\Store\ValidationException;

class ApiGetMessageAction implements Action
{
    public function __invoke(Request $request): JsonResponse
    {
        if (!$request->getQueryParam('messageId')) {
            throw new ActionException('Query params must contain a messageId');
        }
        try {
            $message = (new DiskStore())->getMessage(
                $request->getHeader('x-user-token'),
                $request->getQueryParam('channel'),
                $request->getQueryParam('messageId'),
                $request->getQueryParam('autoack') == 1 ? true : false
            );
        } catch (NotFoundException $error) {
            return new JsonNotFoundResponse($error);
        }
        return new JsonResponse($message);
    }
}
