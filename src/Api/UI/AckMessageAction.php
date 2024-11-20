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

class ApiAckMessageAction implements ActionInterface
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
            throw new HttpNotFoundException($error->getMessage());
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