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
use Kuick\MessageBroker\Api\Security\TokenGuard;
use Kuick\MessageBroker\Infrastructure\MessageStore\MessageNotFoundException;
use Kuick\MessageBroker\Infrastructure\MessageStore\StoreInterface;
use Kuick\UI\ActionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class GetMessageAction implements ActionInterface
{
    public function __construct(private StoreInterface $store, private LoggerInterface $logger)
    {
    }

    public function __invoke(ServerRequestInterface $request): JsonResponse
    {
        $userToken = $request->getHeaderLine(TokenGuard::TOKEN_HEADER);
        $autoAck = $request->getQueryParams()['autoack'] ?? false;
        $autoAck = 1 == $autoAck || 'true' === $autoAck;
        $this->logger->notice('Get message: ' . $request->getQueryParams()['messageId'] . ' by user: ' . md5($userToken) . ', ack: ' . $autoAck);
        try {
            $message = $this->store->getMessage(
                $request->getQueryParams()['channel'],
                $request->getQueryParams()['messageId'],
                $userToken,
                $autoAck,
            );
        } catch (MessageNotFoundException $error) {
            throw new NotFoundException($error->getMessage());
        }
        return new JsonResponse($message);
    }
}
