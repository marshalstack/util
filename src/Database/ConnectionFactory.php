<?php

declare(strict_types=1);

namespace Marshal\Util\Database;

use Doctrine\DBAL\DriverManager;

final class ConnectionFactory
{
    private array $connections = [];

    public function __construct(private array $config)
    {
    }

    public function getConnection(string $database = 'main'): Connection
    {
        if (! \array_key_exists($database, $this->config)) {
            throw new \InvalidArgumentException(\sprintf(
                "Database connection %s not found in config",
                $database
            ));
        }

        if (! \array_key_exists($database, $this->connections)) {
            $this->config[$database]['wrapperClass'] = Connection::class;
            $this->connections[$database] = DriverManager::getConnection($this->config[$database]);
        }

        return $this->connections[$database];
    }
}
