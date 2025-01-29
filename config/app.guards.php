<?php

/**
 * Kuick Framework (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-framework
 * @copyright  Copyright (c) 2010-2025 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://github.com/milejko/kuick-message-broker?tab=MIT-1-ov-file#readme New BSD License
 */

use Kuick\Framework\Config\GuardConfig;
use KuickMessageBroker\Api\Security\TokenGuard;

// security configuration
return [
    new GuardConfig('/api/(message|messages|ack)/(?<channel>[a-zA-Z0-9]+)(.+)?', TokenGuard::class),
];
