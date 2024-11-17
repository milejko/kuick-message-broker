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
use MessageBroker\Store\NotFoundException;
use MessageBroker\Store\ValidationException;

class ApiGetMessageAction implements Action
{
    public function __invoke(Request $request): JsonResponse
    {
        if (!isset($request->query['channel'])) {
            throw new ActionException('Query params must contain a channel name');
        }
        if (!isset($request->query['messageId'])) {
            throw new ActionException('Query params must contain a messageId');
        }
        try {
            $message = (new DiskStore())->getMessage(
                $request->getHeader('x-user-token'),
                $request->query['messageId'],
                $request->query['channel'],
                isset($request->query['autoack']) ? true : false
            );
        } catch (ValidationException $error) {
            throw new ActionException($error->getMessage());
        } catch (NotFoundException $error) {
            throw new ActionException($error->getMessage(), JsonResponse::CODE_NOT_FOUND);
        }
        return new JsonResponse($message);
    }
}
