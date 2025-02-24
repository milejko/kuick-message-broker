<?php

/**
 * Kuick Framework (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-framework
 * @copyright  Copyright (c) 2010-2025 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://github.com/milejko/kuick-message-broker?tab=MIT-1-ov-file#readme New BSD License
 */

use Kuick\Framework\Events\RequestReceivedEvent;
use Kuick\Framework\Kernel;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\EventDispatcher\EventDispatcherInterface;

$projectDir = dirname(__DIR__);
require $projectDir . '/vendor/autoload.php';

// Using .env loader is not recommended from the performance perspective
// uncomment the line below if you really want to use it
//Kuick\Dotenv\DotEnvLoader::fromDirectory($projectDir);

$psr17Factory = new Psr17Factory();

$request = (new ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory, // StreamFactory
))->fromGlobals();

(new Kernel($projectDir))
    ->getContainer()
        ->get(EventDispatcherInterface::class)
            ->dispatch(new RequestReceivedEvent($request));
