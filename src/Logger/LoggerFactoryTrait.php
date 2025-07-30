<?php

/**
 *
 */

declare(strict_types=1);

namespace Marshal\Util\Logger;

use Psr\Log\LoggerInterface;

trait LoggerFactoryTrait
{
    private LoggerFactory $loggerFactory;

    public function getLogger(string $name): LoggerInterface
    {
        return $this->loggerFactory->getLogger($name);
    }

    public function setLoggerFactory(LoggerFactory $loggerFactory): void
    {
        $this->loggerFactory = $loggerFactory;
    }
}
