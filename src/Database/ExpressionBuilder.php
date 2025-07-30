<?php

declare(strict_types=1);

namespace Marshal\Util\Database;

class ExpressionBuilder extends \Doctrine\DBAL\Query\Expression\ExpressionBuilder
{
    public function __construct(private readonly Connection $connection)
    {
        parent::__construct($connection);
    }
}
