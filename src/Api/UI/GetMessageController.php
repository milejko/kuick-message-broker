<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace KuickMessageBroker\Api\UI;

use Kuick\Http\Message\JsonResponse;
use Kuick\Http\NotFoundException;
use KuickMessageBroker\Api\Security\TokenGuard;
use KuickMessageBroker\Infrastructure\MessageStore\MessageNotFoundException;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use OpenApi\Attributes as OAA;

#[OAA\Info(title: 'Message Broker API', version: '1.x')]
#[OAA\Get(
    path: '/api/message/{channel}/{messageId}?autoack={autoack}',
    description: 'Returns a message',
    security: [['Bearer Token' => []]],
    tags: ['api'],
    parameters: [
        new OAA\Parameter(
            name: 'channel',
            in: 'path',
            required: true,
            examples: [new OAA\Examples(example: 'example', value: 'example')]
        ),
        new OAA\Parameter(
            name: 'messageId',
            in: 'path',
            required: true,
        ),
        new OAA\Parameter(
            name: 'autoack',
            in: 'query',
            required: false,
            examples: [
                new OAA\Examples(example: 'true', value: 'true')
            ]
        ),
    ],
    responses: [
        new OAA\Response(
            response: 201,
            description: 'Message created',
            content: new OAA\JsonContent(properties: [
                new OAA\Property(property: 'messageId', type: 'string'),
            ])
        ),
        new OAA\Response(
            response: 401,
            description: 'Token is missing'
        ),
        new OAA\Response(
            response: 403,
            description: 'Token invalid'
        ),
    ]
)]
class GetMessageController
{
    private const LOG_MESSAGE_TEMPLATE = 'Get message: %s@%s by user: %s..., ack: %s';

    public function __construct(private MessageStore $store, private LoggerInterface $logger)
    {
    }

    public function __invoke(string $channel, string $messageId, ServerRequestInterface $request): JsonResponse
    {
        $userToken = $request->getHeaderLine(TokenGuard::TOKEN_HEADER);
        $autoAck = $request->getQueryParams()['autoack'] ?? false;
        $autoAck = 1 == $autoAck || 'true' === $autoAck;
        $this->logger->notice(sprintf(self::LOG_MESSAGE_TEMPLATE, $messageId, $channel, substr($userToken, 7, 5), (int)$autoAck));
        try {
            $message = $this->store->getMessage(
                $channel,
                $messageId,
                $userToken,
            );
            $autoAck && $this->store->ack($channel, $messageId, $userToken);
        } catch (MessageNotFoundException $error) {
            throw new NotFoundException($error->getMessage());
        }
        return new JsonResponse($message + ['acked' => $autoAck]);
    }
}
