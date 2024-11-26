<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Api\Security;

use DI\Attribute\Inject;
use Kuick\Http\BadRequestException;
use Kuick\Http\ForbiddenException;
use Kuick\Http\RequestMethods;
use Kuick\Http\UnauthorizedException;
use Kuick\Security\GuardInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class TokenGuard implements GuardInterface
{
    public const TOKEN_HEADER = 'Authorization';

    private const BEARER_PREFIX = 'Bearer ';
    
    public function __construct(
        #[Inject('kuick.mb.publisher.map')] private string $publisherTokenMap,
        #[Inject('kuick.mb.consumer.map')] private string $consumerTokenMap,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): void
    {
        $channel = $request->getQueryParams()['channel'] ?? null;
        if (null === $channel) {
            throw new BadRequestException('Missing channel parameter');
        }
        $bearerHeader = $request->getHeaderLine(self::TOKEN_HEADER);
        if (!$bearerHeader) {
            throw new UnauthorizedException('Token is missing');
        }
        $requestToken = substr($bearerHeader, strlen(self::BEARER_PREFIX));
        if (RequestMethods::GET == $request->getMethod()) {
            $this->validateToken($this->consumerTokenMap, $requestToken, $channel);
            return;
        }
        $this->validateToken($this->publisherTokenMap, $requestToken, $channel);
    }

    private function validateToken(string $map, string $requestToken, $channel): void
    {
        $channelMap = [];
        parse_str($map, $channelMap);
        $this->logger->debug('Channel map is containg tokens for: ' . count($channelMap) . ' channel(s)');
        if (!isset($channelMap[$channel])) {
            throw new ForbiddenException('No tokens found for this channel');
        }
        $this->logger->debug('Channel has: ' . count($channelMap[$channel]) . ' defined token(s)');
        foreach ($channelMap[$channel] as $token) {
            if ($token == $requestToken) {
                $this->logger->debug('Token matched: ' . substr($requestToken, 0, 5) . '...');
                return;
            }
        }
        throw new ForbiddenException('Token is invalid');
    }
}
