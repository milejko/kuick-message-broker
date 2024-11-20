<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure;

/**
 * Validates object (like channel and userToken)
 */
class ObjectNameValidator
{
    private const PATTERN = '/^[a-z0-9\-\.\_\@]{2,40}$/i';

    public function isValid(string $value): bool
    {
        if (preg_match(self::PATTERN, $value)) {
            return true;
        }
        return false;
    }
}