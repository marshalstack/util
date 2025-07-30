<?php

declare(strict_types=1);

namespace Marshal\Util;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => $this->getCommands(),
            'dependencies' => $this->getDependencies(),
        ];
    }

    private function getCommands(): array
    {
        return [
            'migration:generate' => Database\Migration\MigrationGenerateCommand::class,
            'migration:rollback' => Database\Migration\MigrationRollBackCommand::class,
            'migration:run' => Database\Migration\MigrationRunCommand::class,
            'migration:setup' => Database\Migration\MigrationSetupCommand::class,
            'migration:status' => Database\Migration\MigrationStatusCommand::class,
        ];
    }

    private function getDependencies(): array
    {
        return [
            'factories' => [
                Database\Migration\MigrationGenerateCommand::class => Database\Migration\MigrationCommandFactory::class,
                Database\Migration\MigrationRollBackCommand::class => Database\Migration\MigrationCommandFactory::class,
                Database\Migration\MigrationRunCommand::class => Database\Migration\MigrationCommandFactory::class,
                Database\Migration\MigrationSetupCommand::class => Database\Migration\MigrationCommandFactory::class,
                Database\Migration\MigrationStatusCommand::class => Database\Migration\MigrationCommandFactory::class,
                FileSystem\Local\FileManager::class => FileSystem\Local\FileManagerFactory::class,
            ],
        ];
    }
}
