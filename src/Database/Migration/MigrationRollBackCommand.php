<?php

declare(strict_types= 1);

namespace Marshal\Util\Database\Migration;

use Doctrine\DBAL\Schema\SchemaDiff;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigrationRollBackCommand extends Command
{
    use MigrationCommandTrait;

    public function __construct(protected ContainerInterface $container, string $name)
    {
        parent::__construct($name);
    }

    public function configure(): void
    {
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'The name of the migration to rollback');
        $this->setDescription('Reverse one or more database migrations');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // validate the input
        $input->validate();

        // get details
        $name = $input->getOption('name');
        try {
            $migration = $this->getMigration($name);
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        if (empty($migration)) {
            $io->error("Migration $name not found");
            return Command::FAILURE;
        }

        $diff = $migration['diff'];
        $database = $migration['database'];
        \assert($diff instanceof SchemaDiff);

        // get the database connection
        try {
            $connection = $this->getDatabaseConnection($database);
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        // created tables
        foreach ($diff->getCreatedTables() as $table) {
            // @todo delete the table
        }

        foreach ($diff->getAlteredTables() as $tableDiff) {}

        foreach ($diff->getDroppedTables() as $table) {
            // @todo recreate the table
        }

        $io->success(\sprintf(
            "Migration %s successfully rolled back",
            $name
        ));

        return Command::SUCCESS;
    }
}
