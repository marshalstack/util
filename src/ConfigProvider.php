<?php

declare(strict_types=1);

namespace Marshal\Util;

use Doctrine\DBAL\Types\Types;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            "commands" => $this->getCommands(),
            "dependencies" => $this->getDependencies(),
            "schema" => [
                "types" => $this->getSchemaTypes(),
                "properties" => $this->getSchemaProperties(),
            ],
            "validators" => $this->getValidators(),
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
            "delegators" => [
                Database\Migration\MigrationGenerateCommand::class => [
                    Database\DatabaseAwareDelegatorFactory::class,
                ],
                Database\Migration\MigrationRunCommand::class => [
                    Database\DatabaseAwareDelegatorFactory::class,
                ],
                Database\Migration\MigrationSetupCommand::class => [
                    Database\DatabaseAwareDelegatorFactory::class,
                ],
                Logger\Handler\DatabaseHandler::class => [
                    Database\DatabaseAwareDelegatorFactory::class,
                ],
            ],
            "factories" => [
                Database\Migration\MigrationGenerateCommand::class      => Database\Migration\MigrationCommandFactory::class,
                Database\Migration\MigrationRollBackCommand::class      => Database\Migration\MigrationCommandFactory::class,
                Database\Migration\MigrationRunCommand::class           => Database\Migration\MigrationCommandFactory::class,
                Database\Migration\MigrationSetupCommand::class         => Database\Migration\MigrationCommandFactory::class,
                Database\Migration\MigrationStatusCommand::class        => Database\Migration\MigrationCommandFactory::class,
                FileSystem\Local\FileManager::class                     => FileSystem\Local\FileManagerFactory::class,
                Logger\Handler\DatabaseHandler::class                   => Logger\Handler\DatabaseHandlerFactory::class,
            ],
        ];
    }

    private function getSchemaProperties(): array
    {
        return [
            "marshal::id" => [
                "autoincrement" => true,
                "description" => "Autoincrementing integer ID",
                "label" => "Auto ID",
                "name" => "id",
                "notnull" => true,
                "type" => Types::BIGINT,
            ],
            "marshal::name" => [
                "label" => "Name",
                "description" => "Entry name",
                "name" => "name",
                "notnull" => true,
                "type" => Types::STRING,
                "length" => 255,
            ],
            "marshal::alias" => [
                "label" => "Alias",
                "description" => "Entry alternate name",
                "name" => "alias",
                "type" => Types::STRING,
                "length" => 255,
            ],
            "marshal::event_channel" => [
                "label" => "Log Channel",
                "description" => "Log channel",
                "name" => "channel",
                "notnull" => true,
                "type" => Types::STRING,
                "index" => true,
                "length" => 255,
            ],
            "marshal::log_context" => [
                "label" => "Context",
                "description" => "Log message context data",
                "name" => "context",
                "type" => Types::JSON,
                "platformOptions" => [
                    "jsonb" => true,
                ],
            ],
            "marshal::log_extra" => [
                "label" => "Extra",
                "description" => "Log extra details",
                "name" => "extra",
                "type" => Types::JSON,
                "platformOptions" => [
                    "jsonb" => true,
                ],
            ],
            "marshal::log_level" => [
                "label" => "Log Level",
                "description" => "String indicating log level",
                "name" => "level",
                "index" => true,
                "notnull" => true,
                "type" => Types::STRING,
                "length" => 255,
            ],
            "marshal::log_message" => [
                "label" => "Log Message",
                "description" => "Log message",
                "name" => "message",
                "notnull" => true,
                "type" => Types::TEXT,
            ],
            "marshal::migration_db" => [
                "label" => "Migration DB",
                "description" => "Database name migration belongs to",
                "name" => "db",
                "index" => true,
                "length" => 255,
                "notnull" => true,
                "type" => Types::STRING,
            ],
            "marshal::migration_diff" => [
                "label" => "Migration Diff",
                "description" => "Serialized object containing a schema diff",
                "name" => "diff",
                "convertToPhpType" => false,
                "notnull" => true,
                "type" => Types::BLOB,
            ],
            "marshal::migration_status" => [
                "label" => "Migration Status",
                "description" => "0 or 1 migration status indicator",
                "name" => "status",
                'type' => 'smallint',
                'notnull' => true,
                'default' => 0,
                'index' => true,
            ],
            "marshal::url" => [
                "label" => "URL",
                "description" => "Entry url",
                "name" => "url",
                "type" => Types::STRING,
                "length" => 255,
            ],
            "marshal::image" => [
                "label" => "Image",
                "description" => "Entry featured image",
                "name" => "image",
                "type" => Types::STRING,
                "length" => 255,
            ],
            "marshal::description" => [
                "label" => "Description",
                "description" => "Entry brief description",
                "name" => "description",
                "type" => Types::TEXT,
            ],
            "marshal::created_at" => [
                "label" => "Created At",
                "description" => "Entry creation time",
                "name" => "created_at",
                "type" => Types::DATETIMETZ_IMMUTABLE,
                "notnull" => true,
                "index" => true,
            ],
            "marshal::identifier" => [
                "constraints" => [
                    "unique" => true,
                ],
                "description" => "Entry unique alphanumeric identifier",
                "index" => true,
                "label" => "Unique Identifier",
                "length" => 255,
                "name" => "identifier",
                "notnull" => true,
                "type" => Types::STRING,
            ],
            "marshal::updated_at" => [
                "label" => "Updated At",
                "description" => "Entry last updated time",
                "name" => "updated_at",
                "type" => Types::DATETIMETZ_IMMUTABLE,
                "notnull" => true,
                "index" => true,
            ],
        ];
    }

    private function getSchemaTypes(): array
    {
        return [
            "marshal::entry" => [
                "name" => "Entry",
                "description" => "Generic database entry",
                "properties" => [
                    "marshal::id" => [],
                    "marshal::name" => [],
                    "marshal::alias" => [],
                    "marshal::url" => [],
                    "marshal::image" => [],
                    "marshal::description" => [],
                    "marshal::identifier" => [],
                    "marshal::created_at" => [],
                    "marshal::updated_at" => [],
                ],
            ],
            "marshal::log" => [
                "name" => "",
                "description" => [],
                "inherits" => ["marshal::entry"],
                "meta" => [],
                "properties" => [
                    "marshal::log_channel" => [],
                    "marshal::log_level" => [],
                    "marshal::log_message" => [],
                    "marshal::log_context" => [],
                    "marshal::log_extra" => [],
                    "marshal::description" => [
                        "name" => "message",
                    ],
                    "marshal::created_at" => [
                        "name" => "timestamp",
                    ],
                ],
                "exclude_properties" => [
                    "marshal::alias",
                    "marshal::image",
                    "marshal::name",
                    "marshal::description",
                ],
            ],
            "marshal::migration" => [
                "name" => "",
                "description" => [],
                "inherits" => ["marshal::entry"],
                "meta" => [],
                "properties" => [
                    "marshal::migration_db" => [],
                    "marshal::migration_diff" => [],
                    "marshal::migration_status" => [],
                    "marshal::identifier" => [
                        "name" => "tag",
                    ],
                    "marshal::created_at" => [],
                    "marshal::updated_at" => [],
                ],
                "exclude_properties" => [
                    "marshal::alias",
                    "marshal::image",
                ],
            ],
        ];
    }

    private function getValidators(): array
    {
        return [
            "factories" => [
                Database\Validator\PropertyConfigValidator::class => Database\Validator\PropertyConfigValidatorFactory::class,
                Database\Validator\TypeConfigValidator::class => Database\Validator\TypeConfigValidatorFactory::class,
            ],
        ];
    }
}
