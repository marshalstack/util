<?php

declare(strict_types=1);

namespace Marshal\Util\Database\Migration;

use Marshal\Util\Database\DatabaseAwareInterface;
use Marshal\Util\Database\DatabaseAwareTrait;
use Marshal\Util\Database\Schema\Type;
use Marshal\Util\Database\Schema\TypeManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigrationSetupCommand extends Command implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;
    use MigrationCommandTrait;

    public function __construct(protected ContainerInterface $container, string $name)
    {
        parent::__construct($name);
    }

    public function configure(): void
    {
        $this->setDescription("Setup database migrations. Installs the migration table onto the main database");
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info("Setting up migrations");

        $connection = $this->getDatabaseConnection();
        if ($connection->createSchemaManager()->tableExists('migration')) {
            $io->info("Migrations already setup");
            return Command::SUCCESS;
        }

        // create the migrations table
        $typeManager = $this->container->get(TypeManager::class);
        \assert($typeManager instanceof TypeManager);

        $type = $typeManager->get("marshal::migration");
        \assert($type instanceof Type);

        $schema = $this->buildContentSchema([$type]);
        foreach ($schema->toSql($connection->getDatabasePlatform()) as $createStmt) {
            $connection->executeStatement($createStmt);
        }

        $io->success("Migration table setup");

        return Command::SUCCESS;
    }
}
