<?php

declare(strict_types=1);

namespace Marshal\Util;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'schema' => $this->getSchemaConfig(),
        ];
    }

    private function getDependencies(): array
    {
        return [
            'delegators' => [
                Logger\Handler\DatabaseHandler::class => [
                    Database\DatabaseAwareDelegatorFactory::class,
                ],
            ],
            'factories' => [
                FileSystem\Local\FileManager::class => FileSystem\Local\FileManagerFactory::class,
                Logger\Handler\DatabaseHandler::class => Logger\Handler\DatabaseHandlerFactory::class,
            ],
        ];
    }

    private function getSchemaConfig(): array
    {
        return [
            "marshal::log" => [
                "properties" => [
                    "id" => [
                        "type" => "bigint",
                        "notnull" => true,
                        "autoincrement" => true,
                    ],
                    "channel" => [
                        "type" => "string",
                        "index" => true,
                        "length" => 255,
                    ],
                    "level" => [
                        "type" => "string",
                        "notnull" => true,
                        "index" => true,
                        "length" => 255,
                    ],
                    "message" => [
                        "type" => "text",
                        "notnull" => true,
                    ],
                    "context" => [
                        "type" => "json",
                    ],
                    "extra" => [
                        "type" => "json",
                    ],
                    "timestamp" => [
                        "type" => "datetimetz_immutable",
                        "notnull" => true,
                        "index" => true,
                    ],
                ],
            ],
        ];
    }
}
