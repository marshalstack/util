<?php

declare(strict_types=1);

namespace Marshal\Util\Logger\Handler;

use Marshal\ContentManager\ContentManager;
use Marshal\Util\Database\DatabaseAwareInterface;
use Marshal\Util\Database\DatabaseAwareTrait;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

final class DatabaseHandler extends AbstractProcessingHandler implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    public function __construct(private ContentManager $contentManager)
    {
    }

    public function write(LogRecord $record): void
    {
        // get the content object
        $content = $this->contentManager->get("marshal::log");

        // hydrate the content object
        $content->getProperty("channel")->setValue($record->channel);
        $content->getProperty("level")->setValue($record->level->getName());
        $content->getProperty("message")->setValue($record->formatted);
        $content->getProperty("context")->setValue($record->context);
        $content->getProperty("extra")->setValue($record->extra);
        $content->getProperty("timestamp")->setValue(new \DateTimeImmutable(
            timezone: new \DateTimeZone('UTC')
        ));

        // prepare the query
        $connection = $this->getDatabaseConnection();
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->insert('log');
        foreach ($content->getProperties() as $property) {
            if ($property->isAutoIncrement()) {
                continue;
            }

            $queryBuilder->setValue(
                $property->getIdentifier(),
                $queryBuilder->createNamedParameter(
                    $property->getDatabaseValue($connection->getDatabasePlatform()),
                    $property->getDatabaseType()->getBindingType()
                )
            );
        }

        // execute the query
        $queryBuilder->executeStatement();
    }
}
