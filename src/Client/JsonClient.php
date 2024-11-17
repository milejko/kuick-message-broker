<?php

namespace MessageBroker\Client;

final class JsonClient
{
    private const METHOD_GET = 'GET';
    private const METHOD_POST = 'POST';
    private const METHOD_PUT = 'PUT';
    private const METHOD_DELETE = 'DELETE';

    public function get(string $url, array $headers = [], $payload = ''): array
    {
        return $this->query($url, self::METHOD_GET, $headers, $payload);
    }

    public function post(string $url, array $headers = [], $payload = ''): array
    {
        return $this->query($url, self::METHOD_POST, $headers, $payload);
    }

    public function put(string $url, array $headers = [], $payload = ''): array
    {
        return $this->query($url, self::METHOD_PUT, $headers, $payload);
    }

    public function delete(string $url, array $headers = [], $payload = ''): array
    {
        return $this->query($url, self::METHOD_DELETE, $headers, $payload);
    }

    private function query(string $url, string $method = self::METHOD_GET, array $headers = [], $payload = ''): array
    {
        $headers[] = 'Content-type: application/json';
        return json_decode(
            file_get_contents(
                $url,
                false,
                stream_context_create([
                    'http' => [
                        'content' => $payload,
                        'method' => $method,
                        'header' => implode("\r\n", $headers)
                    ]
                ])
            ),
            true,
            flags: JSON_THROW_ON_ERROR
        );
    }
}
