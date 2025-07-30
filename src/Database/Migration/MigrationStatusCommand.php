<?php

declare(strict_types= 1);

namespace Marshal\Util\Database\Migration;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigrationStatusCommand extends Command
{
    use MigrationCommandTrait;

    public function __construct(protected ContainerInterface $container, string $name)
    {
        parent::__construct($name);
    }

    public function configure(): void
    {
        $this->setDescription('View the status of database schema migrations');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // read migrations status
        $connection = $this->getDatabaseConnection();
        try {
            $result = $connection->fetchAllAssociative(
                "SELECT name, db, status, createdat, updatedat
                FROM migration
                ORDER BY createdat DESC"
            );
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        foreach ($result as &$row) {
            $row['status'] = $row['status'] == 1
                ? 'Done'
                : 'Pending';
        }

        // display status table
        $io->table(['Migration', 'Database', 'Status', 'Created', 'Executed'], $result);

        return Command::SUCCESS;
    }
}
