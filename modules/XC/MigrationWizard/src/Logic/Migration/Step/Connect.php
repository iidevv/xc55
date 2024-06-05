<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Migration\Step;

/**
 * Migration Logic - Connect
 */
class Connect extends \XC\MigrationWizard\Logic\Migration\Step\AStep
{
    /**
     * Fields prefix
     */
    public const FIELDS_PREFIX = 'mw_';

    /**
     * Database username
     *
     * @var string
     */
    private $username;

    /**
     * Database password
     *
     * @var string
     */
    private $password;

    /**
     * Database name
     *
     * @var string
     */
    private $database;

    /**
     * Host name
     *
     * @var string
     */
    private $host = 'localhost';

    /**
     * Port number
     *
     * @var integer
     */
    private $port = 3306;

    /**
     * Socket
     *
     * @var string
     */
    private $socket;

    /**
     * Prefix
     *
     * @var string
     */
    private $prefix = 'xcart_';

    /**
     * Encryption key
     *
     * @var string @todo Move to check requirements step
     */
    private $secret;

    /**
     * Site URL
     *
     * @var string
     */
    private $url;

    /**
     * Site path
     *
     * @var string
     */
    private $path;

    /**
     * Build DSN string
     *
     * @return string
     */
    protected function getDSN()
    {
        $hostspec = $this->host;

        $hostspec .= empty($this->socket) ? (empty($this->port) ? '' : ':' . $this->port)
                : ':' . $this->socket;

        $dsn = 'mysql:dbname=' . $this->database . ';host=' . $hostspec;

        if (!empty($this->port)) {
            $dsn .= ';port=' . $this->port;
        }

        if (!empty($this->socket)) {
            $dsn .= ';port=' . $this->socket;
        }

        return $dsn;
    }

    /**
     * Prepare URL value
     *
     * @return string value
     */
    protected function prepareUrlValue($value)
    {
        return substr($value, -1) === '/' ? substr($value, 0, -1) : $value;
    }

    /**
     * Get database username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get database password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get database name
     *
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Get host name
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get port
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Get socket
     *
     * @return string
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Get secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Get site url
     *
     * @return string
     */
    public function getSiteUrl()
    {
        return $this->url;
    }

    /**
     * Get site url
     *
     * @return string
     */
    public function getSitePath()
    {
        return $this->path;
    }

    /**
     * Get Source Local Flag
     *
     * @return boolean
     */
    public function isSourceSiteLocal()
    {
        return !empty(trim($this->getSitePath()));
    }

    /**
     * Check If We Under Cloud
     *
     * @return boolean
     */
    public function isCloud(): bool
    {
        return (bool)$this->getCurrentServerAddr() && (bool)\Includes\Utils\ConfigParser::getOptions(['service', 'is_cloud']);
    }

    /**
     * Get Current Server IP
     *
     * @return string
     */
    public function getCurrentServerAddr()
    {
        return $_SERVER['SERVER_ADDR'];
    }

    /**
     * Save connection data
     *
     * @return void
     */
    public function saveData()
    {
        $properties = (new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            $origValue = \XLite\Core\Request::getInstance()->{self::FIELDS_PREFIX . $property->name};
            $snitValue = $this->sanitizeValue($origValue);

            if ($this->{$property->name} !== $origValue) {
                if (
                    in_array($property->name, ['password', 'secret'])
                    && !empty($origValue) && empty($snitValue)
                ) {
                    continue;
                }

                $method = 'prepare' . ucfirst($property->name) . 'Value';

                $this->{$property->name} = method_exists($this, $method)
                    ? call_user_func([$this, $method], $origValue)
                    : $origValue;
            }
        }
    }

    /**
     * Sanitize value
     *
     * @param string $value
     *
     * @return string
     */
    public function sanitizeValue($value)
    {
        return preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $value);
    }

    /**
     * Get connection object
     *
     * @return mixed
     */
    public function getConnection()
    {
        static $connection = null;

        if ($connection === null) {
            try {
                $connection = new \PDO(
                    $this->getDSN(),
                    $this->username,
                    $this->password,
                    [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' . ", sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))",]
                );
            } catch (\Exception $e) {
                $connection = false;
                \XLite\Core\TopMessage::addError($e->getMessage());
            }
        }

        return $connection;
    }

    /**
     * Check if connection is available or not
     *
     * @return boolean
     */
    public function isConnectable()
    {
        return $this->getConnection() !== false;
    }

    /**
     * Check if URL is accessible or not
     *
     * @return boolean
     */
    public function isAccessible()
    {
        $request = new \XLite\Core\HTTP\Request($this->url);

        if (
            ($options = \XLite::getInstance()->getOptions('migration_wizard', 'disable_ssl_check'))
            && !empty($options['disable_ssl_check'])
        ) {
            // Disable SSL verification
            $request->setAdditionalOption(CURLOPT_SSL_VERIFYHOST, false);
            $request->setAdditionalOption(CURLOPT_SSL_VERIFYPEER, false);
        }

        $request->setHeader('User-Agent', 'xcart5-MigrationWizard');

        $response = $request->sendRequest();
        $result = ($response && (in_array($response->code, [200, 301])));

        if (!$result) {
            \XLite\Core\TopMessage::addError(
                'The provided store URL is not accessible',
                [
                'X' => !empty($response->code) ? $response->code : 'Empty response'
                ]
            );
        }

        return $result;
    }

    /**
     * Return TRUE if all required connection data is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->isConnectable() && $this->isAccessible();
    }

    /**
     * Return step line title
     *
     * @return string
     */
    public static function getLineTitle()
    {
        return 'Step-Connect';
    }
}
