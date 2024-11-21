<?php

use Kuick\MessageBroker\Infrastructure\DiskStore;
use Kuick\MessageBroker\Infrastructure\StoreInterface;

use function DI\autowire;

return [
    StoreInterface::class => autowire(DiskStore::class),
];