<?php

declare(strict_types=1);

namespace Marshal\Util\Logger\Exception;

class InvalidLoggerConfigException extends \InvalidArgumentException
{
    public function __construct(string $loggerName, array $messages)
    {
        parent::__construct("Invalid logger $loggerName config");
    }
}
