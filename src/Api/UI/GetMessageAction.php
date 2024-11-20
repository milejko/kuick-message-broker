<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Api\UI;

use Kuick\Http\HttpNotFoundException;
use Kuick\Http\JsonResponse;
use Kuick\Http\Request;
use Kuick\UI\ActionInterface;
use Kuick\MessageBroker\Infrastructure\DiskStore;
use Kuick\MessageBroker\Infrastructure\NotFoundException;

class ApiGetMessageAction implements ActionInterface
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $message = (new DiskStore())->getMessage(
                $request->getHeader('x-user-token'),
                $request->getQueryParam('channel'),
                $request->getQueryParam('messageId'),
                $request->getQueryParam('autoack') == 1 ? true : false
            );
        } catch (NotFoundException $error) {
            throw new HttpNotFoundException($error->getMessage());
        }
        return new JsonResponse($message);
    }
}
