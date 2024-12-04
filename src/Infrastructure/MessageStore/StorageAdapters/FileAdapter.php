<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters;

use FilesystemIterator;
use GlobIterator;
use Throwable;

class FileAdapter implements StorageAdapterInterface
{
    private const GC_DIVISOR = 100;

    public function __construct(private string $path)
    {
    }

    public function get(string $namespace, string $key): ?array
    {
        //try to read file - it is faster than check if exists and read
        try {
            $serializedValue = file_get_contents($this->getDataFolderName($namespace) . $this->encodeKey($key));
        } catch (Throwable $error) {
            unset($error); //nothing to do
            return null;
        }
        $value = (new ValueSerializer())->unserialize($serializedValue);
        //expired
        if ((int) ($value['createTime'] + $value['ttl']) < time()) {
            return null;
        }
        return $value;
    }

    public function set(string $namespace, string $key, ?string $value = null, int $ttl = self::MAX_TTL): self
    {
        if ($ttl > self::MAX_TTL) {
            throw new StorageAdapterException("Specified ttl $ttl exceeds " . self::MAX_TTL);
        }
        $fileName = $this->getDataFolderName($namespace) . $this->encodeKey($key);
        $serializedValue = (new ValueSerializer())->serialize($value, $ttl);
        //try to write file, check and create data folder if failed
        try {
            file_put_contents($fileName, $serializedValue);
        } catch (Throwable $error) {
            unset($error); //nothing to report
            $this->checkAndCreateDataFolder($namespace);
            file_put_contents($fileName, $serializedValue);
        }
        //garbage collector
        $this->gc($namespace);
        return $this;
    }

    public function has(string $namespace, string $key): bool
    {
        //need to read the file, ttl needs to be checked
        return null !== $this->get($namespace, $key);
    }

    public function browseKeys(string $namespace, string $pattern = '*'): array
    {
        //GlobIterator is the best performing directory browser for PHP
        $directoryIterator = new GlobIterator($this->getDataFolderName($namespace) . $pattern, FilesystemIterator::KEY_AS_FILENAME);
        $keys = [];
        foreach ($directoryIterator as $item) {
            //file is way to old
            if ($item->getCTime() + self::MAX_TTL < time()) {
                continue;
            }
            $key = $this->decodeKey(basename($item->getPathname()));
            //item needs to read and checked with ttl checker
            if (null === $this->get($namespace, $key)) {
                continue;
            }
            $keys[] = $key;
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

    private function checkAndCreateDataFolder(string $namespace): void
    {
        $dataFolder = rtrim($this->getDataFolderName($namespace), '/');
        //not a directory - create
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0770, true);
            return;
        }
        if (!is_dir($dataFolder) || !is_writable($dataFolder)) {
            throw new StorageAdapterException("$dataFolder is not writeable");
        }
    }

    private function gc(string $namespace): void
    {
        if (rand(0, self::GC_DIVISOR) != 0) {
            return;
        }
        //GlobIterator is the best performing directory browser for PHP
        $directoryIterator = new GlobIterator($this->getDataFolderName($namespace) . '*', FilesystemIterator::KEY_AS_FILENAME);
        //removing expired files
        foreach ($directoryIterator as $item) {
            if (filemtime($item->getRealPath()) + self::MAX_TTL < time()) {
                unlink($item->getRealPath());
            }
        }
    }

    private function getDataFolderName(string $namespace): string
    {
        return $this->path . DIRECTORY_SEPARATOR . urlencode($namespace) . DIRECTORY_SEPARATOR;
    }
}
