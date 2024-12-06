<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\KuickMessageBroker\Unit\Infrastructure\MessageStore\StorageAdapters;

use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\FileAdapter;
use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\RedisAdapter;
use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterException;
use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterFactory;

use function PHPUnit\Framework\assertInstanceOf;

class StorageAdapterFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testIfRedisDsnProvidesRedisAdapter(): void
    {
        $redis = (new StorageAdapterFactory('redis://127.0.0.1:6379'))();
        assertInstanceOf(RedisAdapter::class, $redis);
    }

    public function testIfFileDsnProvidesFileAdapter(): void
    {
        $redis = (new StorageAdapterFactory('file:///tmp'))();
        assertInstanceOf(FileAdapter::class, $redis);
    }
    
    public function testIfUnsupportedAdapterThrowsException(): void
    {
        $this->expectExceptionMessage('DSN invalid');
        $this->expectException(StorageAdapterException::class);
        (new StorageAdapterFactory('mysql://127.0.0.1:3306'))();
    }
}
