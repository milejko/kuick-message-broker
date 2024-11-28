<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters;

use DI\Attribute\Inject;
use Nyholm\Dsn\DsnParser;

class StorageAdapterFactory
{
    public function __construct(#[Inject('kuick.mb.storage.dsn')] private string $dsnString)
    {
    }

    public function __invoke(): StorageAdapterInterface
    {
        $dsn = DsnParser::parse($this->dsnString);
        switch ($dsn->getScheme()) {
            case 'redis':
                return (new RedisClientFactory())($dsn);
            case 'file':
                return new FileAdapter($dsn->getPath());
            default:
                throw new StorageAdapterException('DSN invalid: \'' . $this->dsnString . '\', valid examples: redis://127.0.0.1:6379 or file:///tmp');
        }
    }
}
