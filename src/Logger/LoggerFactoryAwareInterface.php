<?php

/**
 *
 */

declare(strict_types=1);

namespace Marshal\Util\Logger;

use Psr\Log\LoggerInterface;

interface LoggerFactoryAwareInterface
{
    public function setLoggerFactory(LoggerFactory $loggerFactory): void;
    public function getLogger(string $name): LoggerInterface;
}
