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
class JsonErrorResponse extends JsonResponse
{
    public function __construct(private string $message, private int $code = self::CODE_ERROR)
    {
        parent::__construct(['message' => $message], $code);
    }
}