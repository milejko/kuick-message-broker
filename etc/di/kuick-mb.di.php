<?php

use Kuick\MessageBroker\Infrastructure\FilesystemStore;
use Kuick\MessageBroker\Infrastructure\StoreInterface;

use function DI\autowire;

return [
    StoreInterface::class => autowire(FilesystemStore::class),
];