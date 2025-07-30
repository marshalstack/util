<?php

/**
 *
 */

declare(strict_types=1);

namespace Marshal\Util\Logger;

use Psr\Container\ContainerInterface;

final class LoggerFactoryDelegator
{
    public function __invoke(ContainerInterface $container, string $requestedName, callable $callback): object
    {
        $instance = $callback();
        if (! $instance instanceof LoggerFactoryAwareInterface) {
            throw new \RuntimeException(\sprintf(
                "Service %s does not implement %s",
                \get_debug_type($instance),
                LoggerFactoryAwareInterface::class
            ));
        }

        $instance->setLoggerFactory(new LoggerFactory($container->get('config')['loggers'] ?? []));
        return $instance;
    }
}
