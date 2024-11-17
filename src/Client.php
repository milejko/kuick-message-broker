<?php

/**
 * Message Broker Framework
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker;

/**
 * 
 */
class Consumer
{
    private const READ_INTERVAL_MS = 50;

    public function setAuthorization($userName, $userSecret): void
    {

    }

    public function consume(string $channel, callable $callback): void
    {
        while (true) {
            usleep(self::READ_INTERVAL_MS * 1000);
        }
    }
}