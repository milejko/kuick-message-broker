<?php

/**
 * Kuick Message Broker Framework
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker;

/**
 *
 */
class MessageBrokerClient
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
        /*$request = (new Request())
            ->withUri($this->apiAddress . '/api/messages')
            ->withHeader('X-User-Token', $this->userToken)
            ->withMethod(Request::METHOD_POST)
            ->withBody($message)
            ->withQueryParam('channel', $channel)
            ->withQueryParam('ttl', $ttl);
        return (new JsonClient)->query($request);*/
        return [];
    }

    public function consume(string $channel, callable $callback): void
    {
        while (true) {
            usleep(self::READ_INTERVAL_MS * 1000);
        }
    }
}
