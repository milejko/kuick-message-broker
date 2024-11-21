<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

 namespace Kuick\MessageBroker\Infrastructure;

class MessageNotFoundException extends StoreException
{
    protected $message = 'Message not found';
}
