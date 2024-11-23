<?php

use Kuick\MessageBroker\Infrastructure\APCUStore;
use Kuick\MessageBroker\Infrastructure\StoreInterface;

use function DI\autowire;

return [
    'kuick.mb.consumer.tokens' => [],
    'kuick.mb.publisher.tokens' => [],

    StoreInterface::class => autowire(APCUStore::class),
];
