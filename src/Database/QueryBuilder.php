<?php

declare(strict_types=1);

namespace Marshal\Util\Database;

class QueryBuilder extends \Doctrine\DBAL\Query\QueryBuilder
{
    public function __construct(private readonly Connection $connection)
    {
        parent::__construct($connection);
    }

    public function expr(): ExpressionBuilder
    {
        return $this->connection->createExpressionBuilder();
    }
}
