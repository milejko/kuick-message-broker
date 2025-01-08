<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\KuickMessageBroker\Unit\Infrastructure\MessageStore\StorageAdapters;

use Kuick\Redis\RedisMock;
use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\RedisAdapter;
use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterException;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertArrayNotHasKey;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

class RedisAdapterTest extends \PHPUnit\Framework\TestCase
{
    public function testIfEmptyStoreThrowsEntityNotFound(): void
    {
        $dm = new RedisAdapter(new RedisMock());
        $namespace = 'first';
        assertFalse($dm->has($namespace, 'inexistent'));
        assertNull($dm->get($namespace, 'inexistent'));
    }

    public function testIfValuesAreProperlySetAndReveived(): void
    {
        $dm = new RedisAdapter(new RedisMock());
        $namespace = 'second';
        //empty store
        assertFalse($dm->has($namespace, 'foo'));
        //set
        $dm->set($namespace, 'foo', 'bar', 1);
        //check set
        assertTrue($dm->has($namespace, 'foo'));
        assertArrayHasKey('message', $dm->get('second', 'foo'));
        assertArrayHasKey('createTime', $dm->get('second', 'foo'));
        assertArrayHasKey('ttl', $dm->get('second', 'foo'));
        assertEquals(1, $dm->get($namespace, 'foo')['ttl']);
        assertEquals([0 => 'foo'], $dm->browseKeys('second', 'fo*'));
    }

    public function testIfWeirdKeysAreHandledProperly(): void
    {
        $dm = new RedisAdapter(new RedisMock());
        $namespace = './looks/../like/hack';
        $key = 'dir://parent/../../${VAR}/key/../^%##$!@#$%^&*()';
        assertFalse($dm->has($namespace, $key));
        $dm->set($namespace, $key, 'value', 1);
        assertTrue($dm->has($namespace, $key));
        assertEquals('value', $dm->get($namespace, $key)['message']);
        assertEquals([0 => $key], $dm->browseKeys($namespace));
    }

    public function testTtls(): void
    {
        $dm = new RedisAdapter(new RedisMock());
        $namespace = 'ttls';
        $key = 'ttl-test';
        assertFalse($dm->has($namespace, $key));
        $dm->set($namespace, $key, 'value', 1);
        sleep(2);
        assertFalse($dm->has($namespace, $key));
        assertArrayNotHasKey($key, $dm->browseKeys($namespace));
        $this->expectException(StorageAdapterException::class);
        $dm->set($namespace, $key, 'data', 1234567890);
    }
}
