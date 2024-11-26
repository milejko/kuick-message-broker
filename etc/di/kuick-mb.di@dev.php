<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    //some dummy tokens
    'kuick.mb.consumer.token.map' => 'sample-channel[]=reader@pass&sample-channel[]=user@pass',
    'kuick.mb.publisher.token.map' => 'sample-channel[]=publisher@pass',
];