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
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use OpenApi\Attributes as OAA;

#[OAA\Post(
    path: '/api/message/{channel}?ttl={ttl}',
    description: 'Post message',
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
            name: 'ttl',
            in: 'query',
            required: false,
            examples: [
                new OAA\Examples(example: '300', value: '300')
            ]
        ),
    ],
    requestBody: new OAA\RequestBody(
        required: false,
        content: new OAA\JsonContent()
    ),
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
class PostMessageController
{
    private const DEFAULT_MESSAGE_TTL = 300;

    public function __construct(private MessageStore $store, private LoggerInterface $logger)
    {
    }

    public function __invoke(ServerRequestInterface $request): JsonResponse
    {
        $channel = $request->getQueryParams()['channel'] ?? '';
        $ttl = $request->getQueryParams()['ttl'] ?? self::DEFAULT_MESSAGE_TTL;

        $messageId = $this->store->publish($channel, $request->getBody()->getContents(), (int) $ttl > 0 ? $ttl : self::DEFAULT_MESSAGE_TTL);
        $this->logger->info('Published message: ' . $messageId);
        return new JsonResponse(
            [
                'messageId' => $messageId
            ],
            JsonResponse::HTTP_CREATED
        );
    }
}
