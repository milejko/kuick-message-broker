<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\Repositories;

use FilesystemIterator;
use GlobIterator;
use Throwable;

class FileSystemRepository implements RepositoryInterface
{
    public function __construct(private string $path)
    {
        try {
            mkdir($this->path, 0777, true);
        } catch(Throwable $error) {
            unset($error); //nothing to do
        }
    }

    public function get(string $key): ?array
    {
        try {
            $content = file_get_contents($this->path . DIRECTORY_SEPARATOR . $this->encodeKey($key));
        } catch(Throwable $error) {
            unset($error); //nothing to do
            return null;
        }
        return ValueSerializer::unserialize($content);
    }

    public function set(string $key, ?string $value = null, int $ttl = self::MAX_TTL): RepositoryInterface
    {
        file_put_contents($this->path . DIRECTORY_SEPARATOR . $this->encodeKey($key), ValueSerializer::serialize($value, $ttl));
        return $this;
    }

    public function has(string $key): bool
    {
        return file_exists($this->path . DIRECTORY_SEPARATOR . $this->encodeKey($key));
    }

    public function browseKeys(string $pattern = '*'): array
    {
        $directoryIterator = new GlobIterator($this->path . DIRECTORY_SEPARATOR . $pattern, FilesystemIterator::KEY_AS_FILENAME);
        $keys = [];
        foreach ($directoryIterator as $item) {
            $keys[] = $this->decodeKey(basename($item->getPathname()));
        }
        return $keys;
    }

    private function encodeKey(string $key): string
    {
        return urlencode($key);
    }

    private function decodeKey(string $encodedKey): string
    {
        return urldecode($encodedKey);
    }
}