<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Kuick\MessageBroker\Integration\Infrastructure\Repositories;

use Kuick\MessageBroker\Infrastructure\Repositories\EntityNotFoundException;
use Kuick\MessageBroker\Infrastructure\Repositories\FileSystemRepository;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

class FileSystemRepositoryTest extends \PHPUnit\Framework\TestCase
{
    public function testIfEmptyStoreThrowsEntityNotFound(): void
    {
        $dm = new FileSystemRepository(BASE_PATH . '/var/tmp/tests');
        $this->expectException(EntityNotFoundException::class);
        assertFalse($dm->has('inexistent'));
        $dm->get('inexistent');
    }
    public function testIfValuesAreProperlySetAndReveived(): void
    {
        $dm = new FileSystemRepository(BASE_PATH . '/var/tmp/tests/' . time());
        //empty store
        assertFalse($dm->has('foo'));
        //set
        $dm->set('foo', 'bar', 3600);
        //check set
        assertTrue($dm->has('foo'));
        assertArrayHasKey('value', $dm->get('foo'));
        assertArrayHasKey('createTime', $dm->get('foo'));
        assertArrayHasKey('ttl', $dm->get('foo'));
        assertEquals(3600, $dm->get('foo')['ttl']);
        assertEquals([0 => 'foo'], $dm->browseKeys('fo*'));
    }

    public function testIfWeirdKeysAreHandledProperly(): void
    {
        $dm = new FileSystemRepository(BASE_PATH . '/var/tmp/tests/' . time());
        $key = 'dir://parent/../../${VAR}/key/../^%##$!@#$%^&*()';
        assertFalse($dm->has($key));
        $dm->set($key, 'value');
        assertTrue($dm->has($key));
        assertEquals('value', $dm->get($key)['value']);
        assertEquals([0 => $key], $dm->browseKeys('dir*'));
    }
}
