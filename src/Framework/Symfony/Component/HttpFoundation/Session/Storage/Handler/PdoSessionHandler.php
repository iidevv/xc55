<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Framework\Symfony\Component\HttpFoundation\Session\Storage\Handler;

use PDO;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler as BasePdoSessionHandler;

class PdoSessionHandler extends BasePdoSessionHandler
{
    public function __construct($pdoOrDsn = null, array $options = [])
    {
        if (
            !empty($options['db_connection_options'])
            && (
                empty($options['db_connection_options'][PDO::MYSQL_ATTR_SSL_CA])
                || empty($options['db_connection_options'][PDO::MYSQL_ATTR_SSL_CERT])
                || empty($options['db_connection_options'][PDO::MYSQL_ATTR_SSL_KEY])
            )
        ) {
            unset(
                $options['db_connection_options'][PDO::MYSQL_ATTR_SSL_CA],
                $options['db_connection_options'][PDO::MYSQL_ATTR_SSL_CERT],
                $options['db_connection_options'][PDO::MYSQL_ATTR_SSL_KEY]
            );
        }

        if (\is_string($pdoOrDsn)) {
            $url = preg_replace('#^((?:pdo_)?sqlite3?):///#', '$1://localhost/', $pdoOrDsn);
            $parsedUrl = parse_url($url);

            if ($parsedUrl === false) {
                preg_match('/:([^\/\/](.+))@/', $url, $matches);

                if (isset($matches[1])) {
                    $pass = $matches[1];
                    $pdoOrDsn = str_replace($pass, rawurlencode($pass), $pdoOrDsn);
                }
            } else {
                $pass = $parsedUrl['pass'];
            }

            $options['db_password'] = $pass ?? '';
        }

        if (
            isset($_REQUEST['target'])
            && $_REQUEST['target'] === 'event_task'
            && isset($_REQUEST['action'])
            && $_REQUEST['action'] === 'run'
            && isset($_REQUEST['event'])
            && $_REQUEST['event'] === 'import'
        ) {
            $options['lock_mode'] = self::LOCK_NONE;
        }
 $options['lock_mode'] = self::LOCK_NONE;

        parent::__construct($pdoOrDsn, $options);
    }
}
