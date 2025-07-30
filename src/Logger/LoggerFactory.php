<?php

/**
 *
 */

declare(strict_types=1);

namespace Marshal\Util\Logger;

use Monolog\Logger;
use Monolog\Handler\HandlerInterface;
use Monolog\Processor\ProcessorInterface;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    private array $loggers = [];
    private array $validationMessages = [];

    public function __construct(private array $loggersConfig)
    {
    }

    public function getLogger(string $name): LoggerInterface
    {
        if (isset($this->loggers[$name]) && $this->loggers[$name] instanceof LoggerInterface) {
            return $this->loggers[$name];
        }

        if (! $this->isValid($name)) {
            throw new Exception\InvalidLoggerConfigException($name, $this->validationMessages);
        }

        $config = $this->loggersConfig[$name];
        $logger = new Logger($name);

        // push handlers
        foreach ($config['handlers'] ?? [] as $handler => $handlerOptions) {
            try {
                $instance = new $handler(...$handlerOptions);
                if (! $instance instanceof HandlerInterface) {
                    throw new \InvalidArgumentException(\sprintf(
                        "Logger handler %s for logger %s is invalid. Handlers must implement %s",
                        $handler,
                        $name,
                        HandlerInterface::class
                    ));
                }

            } catch (\Throwable $e) {
                throw new \InvalidArgumentException($e->getMessage());
            }

            $logger->pushHandler($instance);
        }

        // push processors
        foreach ($config['processors'] ?? [] as $processor => $processorOptions) {
            try {
                $instance = new $processor(...$processorOptions);
                if (! $instance instanceof ProcessorInterface) {
                    throw new \InvalidArgumentException(\sprintf(
                        "Logger processor %s for logger %s invalid. Processors must implement %s",
                        $processor,
                        $name,
                        ProcessorInterface::class
                    ));
                }
            } catch (\Throwable $e) {
                throw new \InvalidArgumentException($e->getMessage());
            }

            $logger->pushProcessor($instance);
        }

        $this->loggers[$name] = $logger;

        return $logger;
    }

    private function isValid(string $name): bool
    {
        if (! \array_key_exists($name, $this->loggersConfig)) {
            $this->validationMessages[] = \sprintf(
                "Logger %s not found in config",
                $name
            );
        }

        $config = $this->loggersConfig[$name];
        foreach ($config['handlers'] ?? [] as $handler => $handlerOptions) {
            if (! \is_string($handler)) {
                $this->validationMessages[] = \sprintf(
                    "Invalid handler %s for logger %s. Must be a string",
                    \get_debug_type($handler),
                    $name
                );
            }
        }

        foreach ($config['processors'] ?? [] as $processor => $processorOptions) {
            if (! \is_string($processor)) {
                $this->validationMessages[] = \sprintf(
                    "Invalid processor %s for logger %s. Must be a string",
                    \get_debug_type($processor),
                    $name
                );
            }
        }

        return empty($this->validationMessages) ? TRUE : FALSE;
    }
}
