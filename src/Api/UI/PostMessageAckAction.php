<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Api\UI;

use Kuick\MessageBroker\Api\Security\TokenGuard;
use Kuick\UI\ActionInterface;
use Kuick\MessageBroker\Infrastructure\DiskStore;
use Kuick\MessageBroker\Infrastructure\NotFoundException;
use Kuick\UI\UINotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PostMessageAckAction implements ActionInterface
{
    public function __invoke(Request $request): JsonResponse
    {
        $channel = $request->query->get('channel');
        $messageId = $request->query->get('messageId');
        $userLabel = md5($request->headers->get(TokenGuard::TOKEN_HEADER));
        try {
            (new DiskStore())->ack(
                $userLabel,
                $channel,
                $messageId,
            );
        } catch (NotFoundException $error) {
            throw new UINotFoundException($error->getMessage());
        }
        return new JsonResponse(
            [
                'acked' => true,
                'channel' => $channel,
                'messageId' => $messageId,
            ],
            JsonResponse::HTTP_ACCEPTED
        );
    }
}
