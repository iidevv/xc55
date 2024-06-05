<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Domain;

use Doctrine\DBAL\Connection;

final class ServiceDataDomain
{
    private Connection $connection;

    public function __construct(
        Connection $connection
    ) {
        $this->connection = $connection;
    }

    public function setData(string $name, string $value): void
    {
        if ($name) {
            $this->connection->executeQuery(
                'REPLACE INTO service_data (`name`, `value`) VALUES (:name, :value)',
                ['name' => $name, 'value' => $value]
            );
        }
    }
}
