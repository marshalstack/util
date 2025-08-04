<?php

declare(strict_types=1);

namespace Marshal\Util;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    private function getDependencies(): array
    {
        return [
            'factories' => [
                FileSystem\Local\FileManager::class => FileSystem\Local\FileManagerFactory::class,
            ],
        ];
    }
}
