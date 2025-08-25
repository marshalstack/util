<?php

declare(strict_types=1);

namespace Marshal\Util\Logger\Handler;

use Marshal\ContentManager\ContentManager;
use Psr\Container\ContainerInterface;

final class DatabaseHandlerFactory
{
    public function __invoke(ContainerInterface $container): DatabaseHandler
    {
        $contentManger = $container->get(ContentManager::class);
        \assert($contentManger instanceof ContentManager);

        return new DatabaseHandler($contentManger);
    }
}
