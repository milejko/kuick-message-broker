<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Store;

use Exception;

class StoreException extends Exception
{
    protected $message = 'Invalid channel or userToken. 2-40 alphanumeric characters and ". - _ @" are allowed.';
}