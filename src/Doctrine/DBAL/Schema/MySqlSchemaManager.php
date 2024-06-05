<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Doctrine\DBAL\Schema;

use Doctrine\DBAL\Exception as DBALException;

class MySqlSchemaManager extends \Doctrine\DBAL\Schema\MySQLSchemaManager
{
    /**
     * @return string[]
     * @throws DBALException
     */
    public function listTableNames(): array
    {
        $params = $this->_conn->getParams();

        $tablePrefix = $params['driverOptions']['table_prefix'] ?? '';

        return $tablePrefix
            ? preg_grep('/^' . preg_quote($tablePrefix . '_', '.+/') . '/Ss', parent::listTableNames())
            : parent::listTableNames();
    }
}
