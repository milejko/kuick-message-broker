<?php

/**
 * Message Broker Framework
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker;

use MessageBroker\Client\JsonClient;

/**
 * 
 */
class Client
{
    private const READ_INTERVAL_MS = 100;
    private string $apiAddress;
    private string $userToken;

    public function setApiAddress(string $apiAddress): self
    {
        $this->apiAddress = $apiAddress;
        return $this;
    }

    public function setAuthorization(string $userToken): self
    {
        $this->userToken = $userToken;
        return $this;
    }

    public function publish(string $channel, string $message, int $ttl = 300): array
    {
        return (new JsonClient)->post($this->apiAddress . '/api/messages?channel=' . $channel . '&ttl=' . $ttl, ['X-User-Token' => $this->userToken], $message);
    }

    public function consume(string $channel, callable $callback): void
    {
        while (true) {
            usleep(self::READ_INTERVAL_MS * 1000);            
        }
    }
}