<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Api\Security;

use Kuick\App\AppConfig;
use Kuick\Security\GuardInterface;
use Kuick\UI\UIBadRequestException;
use Kuick\UI\UIForbiddenException;
use Kuick\UI\UIUnauthorizedException;
use Symfony\Component\HttpFoundation\Request;

class TokenGuard implements GuardInterface
{
    public const CHANNEL_TOKEN_CONFIG_KEY = 'kuick_message_broker_channel_tokens';
    public const TOKEN_HEADER = 'Authorization';

    private const BEARER_TOKEN_TEMPLATE = 'Bearer %s';
    private const ERROR_MISSING_CHANNEL = "Request query is missing 'channel'";
    private const ERROR_MISSING_TOKEN = 'Token is missing';
    private const ERROR_INVALID_TOKEN = 'Token is invalid';
    private const ERROR_NO_CHANNEL_TOKENS = 'No tokens found for this channel';

    public function __construct(private AppConfig $appConfig) {}

    public function __invoke(Request $request): void
    {
        $channel = $request->get('channel');
        if (null === $channel) {
            throw new UIBadRequestException(self::ERROR_MISSING_CHANNEL);
        }
        $requestToken = $request->headers->get(self::TOKEN_HEADER);
        if (null === $requestToken) {
            throw new UIUnauthorizedException(self::ERROR_MISSING_TOKEN);
        }
        if (!isset($this->appConfig->get(self::CHANNEL_TOKEN_CONFIG_KEY)[$channel])) {
            throw new UIForbiddenException(self::ERROR_NO_CHANNEL_TOKENS);
        }
        foreach ($this->appConfig->get(self::CHANNEL_TOKEN_CONFIG_KEY)[$channel] as $token) {
            $expectedToken = sprintf(self::BEARER_TOKEN_TEMPLATE, $token);
            //token match
            if ($requestToken == $expectedToken) {
                return;
            }
        }
        throw new UIForbiddenException(self::ERROR_INVALID_TOKEN);
    }
}
