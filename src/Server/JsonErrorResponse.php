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

/**
 * 
 */
class JsonErrorResponse extends JsonResponse
{
    public function __construct(Throwable $error)
    {
        $code = $error->getCode() == 0 ? self::CODE_ERROR : $error->getCode();
        parent::__construct(['error' => $error->getMessage()], $code);
    }
}