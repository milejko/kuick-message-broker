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
    public function __construct(private array $data, private int $code = 200)
    {
    }

    public function send(): void
    {
        header('Content-type: application/json', true, $this->code);
        echo json_encode($this->data);
        exit;
    }
}