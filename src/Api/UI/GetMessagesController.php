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
use KuickMessageBroker\Api\Security\TokenGuard;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use OpenApi\Attributes as OAA;

#[OAA\Get(
    path: '/api/messages/{channel}',
    description: 'Returns a message list',
    security: [['Bearer Token' => []]],
    tags: ['api'],
    parameters: [
        new OAA\Parameter(
            name: 'channel',
            in: 'path',
            required: true,
            examples: [new OAA\Examples(example: 'example', value: 'example')]
        ),
    ],
    responses: [
        new OAA\Response(
            response: 200,
            description: 'Returns a message list',
            content: new OAA\JsonContent(
                items: new OAA\Items(
                    type: 'object',
                    properties: [
                        new OAA\Property(property: 'createTime', type: 'integer'),
                        new OAA\Property(property: 'ttl', type: 'integer'),
                        new OAA\Property(property: 'messageId', type: 'string'),
                    ]
                )
            ),
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
class GetMessagesController
{
    public function __construct(private MessageStore $store, private LoggerInterface $logger)
    {
    }

    public function __invoke(ServerRequestInterface $request): JsonResponse
    {
        $channel = $request->getQueryParams()['channel'] ?? '';
        $userToken = $request->getHeaderLine(TokenGuard::TOKEN_HEADER);
        $messages = $this->store->getMessages(
            $channel,
            $userToken,
        );
        $this->logger->info('Listing messages for user: ' . md5($userToken) . ' list contains: ' . count($messages) . ' messages');
        return new JsonResponse($messages);
    }
}
