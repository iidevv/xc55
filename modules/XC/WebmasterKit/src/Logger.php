<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\WebmasterKit;

use XCart\Extender\Mapping\Extender;

/**
 * Logger
 * @Extender\Mixin
 */
abstract class Logger extends \XLite\Logger implements \Doctrine\DBAL\Logging\SQLLogger
{
    /**
     * Query data
     *
     * @var   array
     */
    protected $query;

    /**
     * Count
     *
     * @var   integer
     */
    protected $count = 0;

    /**
     * @var string regexp query filter
     */
    protected $regexp;

    public function __construct()
    {
        parent::__construct();

        $this->regexp = \XLite\Core\Config::getInstance()->XC->WebmasterKit->logSQLRegExp ?? '';
    }

    /**
     * Logs a SQL statement somewhere.
     *
     * @param string $sql    The SQL to be executed.
     * @param array  $params The SQL parameters.
     * @param array  $types  The SQL parameter types.
     *
     * @return void
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->query = [
            'sql'    => $sql,
            'params' => $params,
            'start'  => microtime(true),
        ];
    }

    /**
     * Mark the last started query as stopped. This can be used for timing of queries.
     *
     * @return void
     */
    public function stopQuery()
    {
        if (!empty($this->regexp) && !preg_match($this->regexp, $this->query['sql'])) {
            unset($this->query);
            return;
        }

        $duration = microtime(true) - $this->query['start'];

        $params = [];
        if ($this->query['params']) {
            foreach ($this->query['params'] as $v) {
                $params[] = var_export($v, true);
            }
        }

        $this->count++;

        static::getLogger('sql')->debug('', [
            'Query #' . $this->count => $this->query['sql'],
            'Parameters' => $params,
            'Duration' => round($duration, 4) . 'sec.',
            'Doctrine UnitOfWork size' => \XLite\Core\Database::getEM()->getUnitOfWork()->size(),
            'trace' => true
        ]);

        \XC\WebmasterKit\Core\SlowLog::getInstance()->logQuery(
            $this->query['sql'],
            $duration,
            debug_backtrace()
        );

        unset($this->query);
    }
}
