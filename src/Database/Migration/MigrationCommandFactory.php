<?php

declare(strict_types= 1);

namespace Marshal\Util\Database\Migration;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;

final class MigrationCommandFactory
{
    public function __invoke(ContainerInterface $container, $requestedName): object
    {
        $commands = $container->get('config')['commands'] ?? [];

        // check for requested command in config
        if (! \in_array($requestedName, \array_values($commands), true)) {
            throw new \InvalidArgumentException(\sprintf(
                "Invalid factory %s for requested class %s",
                self::class,
                $requestedName
            ));
        }

        $commandName = null;
        foreach ($commands as $name => $command) {
            if ($command === $requestedName) {
                $commandName = $name;
            }
        }
        try {
            $command = new $requestedName($container, $commandName);
            if (! $command instanceof Command) {
                throw new \InvalidArgumentException(\sprintf(
                    "Expected command instance to extend %s, given %s instead",
                    Command::class,
                    \get_debug_type($command)
                ));
            }

        } catch (\Throwable $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

        return $command;
    }
}
