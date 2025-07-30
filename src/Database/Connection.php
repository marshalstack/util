<?php

declare(strict_types=1);

namespace Marshal\Util\Database;

class Connection extends \Doctrine\DBAL\Connection
{
    public function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this);
    }

    public function createExpressionBuilder(): ExpressionBuilder
    {
        return new ExpressionBuilder($this);
    }
}
