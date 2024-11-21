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
use Kuick\Http\NotFoundException;
use Kuick\Http\Request;
use Kuick\MessageBroker\Api\Security\TokenGuard;
use Kuick\MessageBroker\Infrastructure\MessageNotFoundException;
use Kuick\UI\ActionInterface;
use Kuick\MessageBroker\Infrastructure\StoreInterface;

class GetMessageAction implements ActionInterface
{
    public function __construct(private StoreInterface $store)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $userLabel = md5($request->headers->get(TokenGuard::TOKEN_HEADER));
        try {
            $message = $this->store->getMessage(
                $userLabel,
                $request->query->get('channel'),
                $request->query->get('messageId'),
                ($request->query->get('autoack') === 'true' || $request->query->get('autoack') === '1'),
            );
        } catch (MessageNotFoundException $error) {
            throw new NotFoundException($error->getMessage());
        }
        return new JsonResponse($message);
    }
}
