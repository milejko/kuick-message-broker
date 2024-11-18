<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Server;

use Throwable;

class JsonUnauthorizedResponse extends JsonResponse
{
    public function __construct(Throwable $error)
    {
        parent::__construct(['error' => $error->getMessage()], self::CODE_UNAUTHORIZED);
    }
}