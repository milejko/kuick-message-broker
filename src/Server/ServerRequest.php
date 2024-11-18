<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Server;

/**
 * 
 */
class ServerRequest extends Request
{
    private const HTTP_HEADER_PREFIX = 'HTTP_';

    public function __construct(private array $serverVariables, string $body)
    {
        $this->withMethod($this->getServerVariable('REQUEST_METHOD', self::METHOD_GET));
        $this->withUri(
            strpos($this->getServerVariable('SERVER_PROTOCOL'), 'HTTPS') ? 'https://' : 'http://' .
            $this->getServerVariable('HTTP_HOST') . $this->getServerVariable('REQUEST_URI')
        );
        $this->withBody($body);
        //headers
        foreach ($serverVariables as $name => $value) {
            if (!str_starts_with($name, self::HTTP_HEADER_PREFIX)) {
                continue;
            }
            $this->withHeader(str_replace('_', '-', substr($name, strlen(self::HTTP_HEADER_PREFIX))), $value);
        }
    }

    private function getServerVariable(string $key, string $default = ''): mixed
    {
        return isset($this->serverVariables[$key]) ? $this->serverVariables[$key]: $default;
    }
}