<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Domain;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class ServiceLicenseDomain
{
    private Connection $connection;

    public function __construct(
        Connection $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function getLicenseKey(): string
    {
        return $this->connection->fetchOne('SELECT key_value FROM service_licenses WHERE module_id = :name', ['name' => 'CDev-Core']) ?: '';
    }
}
