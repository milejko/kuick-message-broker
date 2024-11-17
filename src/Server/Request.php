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
class Request
{
    private const HTTP_HEADER_PREFIX = 'HTTP_';

    public readonly string $host;
    public readonly string $method;
    public readonly string $uri;
    public readonly string $path;
    public readonly array $query;

    private array $headers = [];

    public function __construct(private array $serverVariables, private string $input)
    {
        $this->host = $this->get('HTTP_HOST');
        $this->method = $this->get('REQUEST_METHOD', 'GET');
        $this->uri = $this->get('REQUEST_URI');

        //path & query
        $parsedUrl = parse_url($this->uri);
        $query = [];
        isset($parsedUrl['query']) ? parse_str($parsedUrl['query'], $query) : [];
        $this->path = isset($parsedUrl['path']) ? ($parsedUrl['path'] == '/' ? $parsedUrl['path'] : rtrim($parsedUrl['path'], '/')) : '';
        $this->query = $query;

        //headers
        foreach ($serverVariables as $name => $value) {
            if (!str_starts_with($name, self::HTTP_HEADER_PREFIX)) {
                continue;
            }
            $this->headers[strtolower(str_replace('_', '-', substr($name, strlen(self::HTTP_HEADER_PREFIX))))] = $value;
        }
    }

    public function getPayload(): string
    {
        return $this->input;
    }

    public function getPayloadAsArray(): array
    {
        parse_str($this->input, $output);
        return $output;
    }

    public function getHeader(string $name): string
    {
        foreach ($this->headers as $headerName => $value) {
            if (strtolower($name) == $headerName) {
                return $value;
            }
        }
        return '';
    }

    private function get(string $key, string $default = ''): mixed
    {
        return isset($this->serverVariables[$key]) ? $this->serverVariables[$key]: $default;
    }
}