<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace KuickMessageBroker\Api\UI;

use Kuick\Http\JsonResponse;
use Kuick\Http\NotFoundException;
use KuickMessageBroker\Api\Security\TokenGuard;
use KuickMessageBroker\Infrastructure\MessageStore\MessageNotFoundException;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Kuick\UI\ActionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class GetMessageAction implements ActionInterface
{
    private const LOG_MESSAGE_TEMPLATE = 'Get message: %s by user: %s..., ack: %s';

    public function __construct(private MessageStore $store, private LoggerInterface $logger)
    {
    }

    public function __invoke(ServerRequestInterface $request): JsonResponse
    {
        $userToken = $request->getHeaderLine(TokenGuard::TOKEN_HEADER);
        $autoAck = $request->getQueryParams()['autoack'] ?? false;
        $autoAck = 1 == $autoAck || 'true' === $autoAck;
        $this->logger->notice(sprintf(self::LOG_MESSAGE_TEMPLATE, $request->getQueryParams()['messageId'], substr($userToken, 7, 5), (int)$autoAck));
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
