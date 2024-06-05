<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Doctrine\DBAL\Driver\PDO\MySQL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\AbstractMySQLDriver;
use Doctrine\DBAL\Driver\PDO\Connection as PDOConnection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use PDO;
use XCart\Doctrine\DBAL\Schema\MySqlSchemaManager;

class Driver extends AbstractMySQLDriver
{
    /**
     * {@inheritdoc}
     *
     * @return PDOConnection
     */
    public function connect(array $params): PDOConnection
    {
        $driverOptions = $params['driverOptions'] ?? [];

        unset($driverOptions['table_prefix']);

        if (!empty($params['persistent'])) {
            $driverOptions[PDO::ATTR_PERSISTENT] = true;
        }

        if (
            empty($driverOptions[PDO::MYSQL_ATTR_SSL_CA])
            || empty($driverOptions[PDO::MYSQL_ATTR_SSL_CERT])
            || empty($driverOptions[PDO::MYSQL_ATTR_SSL_KEY])
        ) {
            unset(
                $driverOptions[PDO::MYSQL_ATTR_SSL_CA],
                $driverOptions[PDO::MYSQL_ATTR_SSL_CERT],
                $driverOptions[PDO::MYSQL_ATTR_SSL_KEY]
            );
        }

        if (isset($params['url'])) {
            $params['password'] = \parse_url($params['url'], PHP_URL_PASS) ?? $params['password'] ?? '';

            if (!empty($params['needPasswordDecoded'])) {
                $params['password'] = \rawurldecode($params['password']);
            }
        }

        $pdo = new PDO(
            $this->constructPdoDsn($params),
            $params['user'] ?? '',
            $params['password'] ?? '',
            $driverOptions
        );

        return new PDOConnection($pdo);
    }

    /**
     * Constructs the MySQL PDO DSN.
     *
     * @param array<string, mixed> $params
     *
     * @return string The DSN.
     */
    protected function constructPdoDsn(array $params): string
    {
        $dsn = 'mysql:';
        if (isset($params['host']) && $params['host'] !== '') {
            $dsn .= 'host=' . $params['host'] . ';';
        }

        if (isset($params['port'])) {
            $dsn .= 'port=' . $params['port'] . ';';
        }

        if (isset($params['dbname'])) {
            $dsn .= 'dbname=' . $params['dbname'] . ';';
        }

        if (isset($params['unix_socket'])) {
            $dsn .= 'unix_socket=' . $params['unix_socket'] . ';';
        }

        if (isset($params['charset'])) {
            $dsn .= 'charset=' . $params['charset'] . ';';
        }

        return $dsn;
    }

    /**
     * @param Connection       $conn
     * @param AbstractPlatform $platform
     *
     * @return AbstractSchemaManager
     */
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform): AbstractSchemaManager
    {
        return new MySqlSchemaManager($conn, $platform);
    }
}
