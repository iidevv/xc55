<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Doctrine\FixtureLoader;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Exception as DBALException;
use Throwable;

final class SQLLoader
{
    private Connection $connection;

    public function __construct(
        Connection $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * @throws DriverException
     * @throws DBALException
     * @throws Throwable
     */
    public function loadSQL(string $sql): int
    {
        return $this->connection->transactional(function () use ($sql) {
            return $this->connection->executeStatement($sql);
        });
    }
}
