<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use function DI\env;

return [
    'kuick.mb.consumer.map' => env('KUICK_MB_CONSUMER_MAP', 'example[]=user@pass'),
    'kuick.mb.publisher.map' => env('KUICK_MB_PUBLISHER_MAP', 'example[]=user@pass'),

    'kuick.mb.storage.dsn' => env('KUICK_MB_STORAGE_DSN', 'file:///var/www/html/var/storage'),
];