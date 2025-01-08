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

#[OAA\Post(
    path: '/api/message/ack/{channel}/{messageId}',
    description: 'Post message ack',
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
    ],
    responses: [
        new OAA\Response(
            response: 200,
            description: 'Acknowledge message',
            content: new OAA\JsonContent(properties: [
                new OAA\Property(property: 'messageId', type: 'string'),
                new OAA\Property(property: 'acked', type: 'boolean'),
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
        new OAA\Response(
            response: 404,
            description: 'Message not found'
        ),
    ]
)]
class PostMessageAckController
{
    public function __construct(private MessageStore $store, private LoggerInterface $logger)
    {
    }

    public function __invoke(string $channel, string $messageId, ServerRequestInterface $request): JsonResponse
    {
        $userToken = $request->getHeaderLine(TokenGuard::TOKEN_HEADER);
        try {
            $this->store->ack(
                $channel,
                $messageId,
                $userToken,
            );
            $this->logger->info('Acked message: ' . $messageId . ' by user: ' . md5($userToken));
        } catch (MessageNotFoundException $error) {
            throw new NotFoundException($error->getMessage());
        }
        return new JsonResponse(
            [
                'messageId' => $messageId,
                'acked' => true,
            ],
            JsonResponse::HTTP_ACCEPTED
        );
    }
}
