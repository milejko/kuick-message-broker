<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterFactory;
use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterInterface;

use function DI\factory;

return [
    StorageAdapterInterface::class => factory(StorageAdapterFactory::class),
];