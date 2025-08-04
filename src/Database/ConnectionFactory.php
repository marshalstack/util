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

    public function getConnection(string $database = "marshal"): Connection
    {
        if (! \array_key_exists($database, $this->config)) {
            throw new \InvalidArgumentException(\sprintf(
                "Database connection %s not found in config",
                $database
            ));
        }

        if (! \array_key_exists($database, $this->connections)) {
            // wrap the DBALConnection
            $this->config[$database]['wrapperClass'] = Connection::class;
            $connection = DriverManager::getConnection($this->config[$database]);

            // add custom types
            foreach ($this->config[$database]['types'] ?? [] as $typeName => $typeClass) {
                $connection->getDatabasePlatform()->registerDoctrineTypeMapping($typeName, $typeClass);
            }

            $this->connections[$database] = $connection;
        }

        return $this->connections[$database];
    }
}
