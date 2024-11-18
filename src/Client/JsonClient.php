<?php

namespace MessageBroker\Client;

use MessageBroker\Server\Request;

final class JsonClient
{
    public function query(Request $request): array
    {
        $headers = $request->getHeaders();
        $headers[] = 'Content-type: application/json';
        return json_decode(
            file_get_contents(
                $request->uri,
                false,
                stream_context_create([
                    'http' => [
                        'content' => $request->body,
                        'method' => $request->method,
                        'header' => implode("\r\n", $request->getHeaders())
                    ]
                ])
            ),
            true,
            flags: JSON_THROW_ON_ERROR
        );
    }
}
