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
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_HEAD = 'HEAD';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_OPTIONS = 'OPTIONS';

    public string $method;
    public string $uri;
    public string $path;
    public string $body;

    private array $headers = [];
    private array $queryParams = [];

    public function withMethod(string $method): self
    {
        $availableMethods = [
            self::METHOD_GET,
            self::METHOD_POST,
            self::METHOD_DELETE,
            self::METHOD_HEAD,
            self::METHOD_PUT,
            self::METHOD_PATCH,
            self::METHOD_OPTIONS,
        ];
        if (!in_array($method, $availableMethods)) {
            throw new RequestException();
        }
        $this->method = $method;
        return $this;
    }

    public function withUri(string $uri): self
    {
        $this->uri = $uri;
        $parsedUrl = parse_url($this->uri);
        isset($parsedUrl['query']) ? parse_str($parsedUrl['query'], $queryParams) : [];
        $this->queryParams = is_array($queryParams) ? $queryParams : [];
        $this->path = isset($parsedUrl['path']) ? ($parsedUrl['path'] == '/' ? $parsedUrl['path'] : rtrim($parsedUrl['path'], '/')) : '';
        return $this;
    }

    public function withBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function withQueryParam(string $name, string $value): self
    {
        $this->queryParams[$name] = $value;
        return $this;
    }

    public function getHeaders(): array
    {
        $headers = [];
        foreach ($this->getHeaders() as $headerName => $headerValue) {
            $headers[] = $headerName . ': ' . $headerValue;
        }
        return $headers;
    }

    public function getHeader(string $name): string
    {
        foreach ($this->headers as $headerName => $value) {
            if (strtolower($name) == strtolower($headerName)) {
                return $value;
            }
        }
        return '';
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getQueryParam(string $name): ?string
    {
        return isset($this->queryParams[$name]) ? $this->queryParams[$name] : null;
    }
}