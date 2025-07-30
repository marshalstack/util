<?php

declare(strict_types=1);

namespace Marshal\Util\FileSystem\Local;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Psr\Container\ContainerInterface;

final class FileManagerFactory
{
    public function __invoke(ContainerInterface $container): FileManager
    {
        $adapter = new LocalFilesystemAdapter(\getcwd());
        $filesystem = new Filesystem($adapter);

        return new FileManager($filesystem);
    }
}
