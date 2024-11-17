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
class JsonResponse
{
    public const CODE_OK = 200;
    public const CODE_ACCEPTED = 202;
    public const CODE_NO_CONTENT = 204;
    
    public const CODE_MOVED_PERMANENTLY = 301;

    public const CODE_BAD_REQUEST = 400;
    public const CODE_UNAUTHORIZED = 401;
    public const CODE_FORBIDDEN = 403;
    public const CODE_NOT_FOUND = 404;

    public const CODE_ERROR = 500;

    public function __construct(private array $data, private int $code = self::CODE_OK)
    {
    }

    public function send(): void
    {
        header('Content-type: application/json', true, $this->code);
        echo json_encode($this->data);
        exit;
    }
}