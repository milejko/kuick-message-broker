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
use Kuick\Http\Request;
use Kuick\Http\UnauthorizedException;
use Kuick\Security\GuardInterface;

class TokenGuard implements GuardInterface
{
    public const TOKEN_HEADER = 'Authorization';

    private const BEARER_PREFIX = 'Bearer ';
    private const ERROR_MISSING_CHANNEL = "Request query is missing 'channel'";
    private const ERROR_MISSING_TOKEN = 'Token is missing';
    private const ERROR_INVALID_TOKEN = 'Token is invalid';
    private const ERROR_NO_CHANNEL_TOKENS = 'No tokens found for this channel';

    public function __construct(
        #[Inject('kuick.mb.publisher.tokens')] private array $publisherTokens,
        #[Inject('kuick.mb.consumer.tokens')] private array $consumerTokens,
    ) {
    }

    public function __invoke(Request $request): void
    {
        $channel = $request->get('channel');
        if (null === $channel) {
            throw new BadRequestException(self::ERROR_MISSING_CHANNEL);
        }
        $bearerHeader = $request->headers->get(self::TOKEN_HEADER);
        if (null === $bearerHeader) {
            throw new UnauthorizedException(self::ERROR_MISSING_TOKEN);
        }
        $requestToken = substr($bearerHeader, strlen(self::BEARER_PREFIX));
        if (Request::METHOD_GET == $request->getMethod()) {
            $this->validateToken($this->consumerTokens, $requestToken, $channel);
            return;
        }
        $this->validateToken($this->publisherTokens, $requestToken, $channel);
    }

    private function validateToken(array $store, string $requestToken, $channel): void
    {
        if (!isset($store[$channel])) {
            throw new ForbiddenException(self::ERROR_NO_CHANNEL_TOKENS);
        }
        if (in_array($requestToken, $store[$channel])) {
            return;
        }
        throw new ForbiddenException(self::ERROR_INVALID_TOKEN);
    }
}
