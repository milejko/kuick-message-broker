<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Server;

/**
 * 
 */
class Kernel
{
    private array $config;
    private readonly array $env;
    private readonly Request $request;
    private readonly Router $router;

    public function setGlobalContext(array $env, array $server, string $input): self
    {
        $config = [];
        foreach (glob(BASE_PATH . '/etc/*.php') as $configFileName) {
            $config[basename($configFileName)] = include $configFileName;
        }
        $this->config = $config;
        $this->env =  $env;
        $this->request = new ServerRequest($server, $input);
        $this->router = new Router(
            (isset($this->config['routes.php']) && is_array($this->config['routes.php'])) ? $this->config['routes.php'] : []
        );
        return $this;
    }

    public function run(): void
    {
        $this->router
            ->execute($this->request)
            ->send();
    }
}
