<?php

declare(strict_types= 1);

namespace Marshal\Util\Database\Migration;

use Marshal\Util\Database\DatabaseAwareInterface;
use Marshal\Util\Database\DatabaseAwareTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigrationStatusCommand extends Command implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

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
        $collection = $connection->createQueryBuilder()
            ->select('m.*')
            ->from('migration', 'm')
            ->orderBy('createdat', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        $result = [];
        foreach ($collection as $row) {
            $row['status'] = $row['status'] == 1
                ? 'Done'
                : 'Pending';

            $result[] = [
                'migration' => $row['name'],
                'database' => $row['db'],
                'status' => $row['status'],
                'created' => $row['createdat']->format('c'),
                'executed' => $row['updatedat'] ? $row['updatedat']->format('c') : null,
            ];
        }

        // display status table
        $io->table(['Migration', 'Database', 'Status', 'Created', 'Executed'], $result);

        return Command::SUCCESS;
    }
}
