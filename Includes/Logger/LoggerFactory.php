<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Logger;

use Monolog\Logger;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use XCart\Container;

use const LOG_ALERT;
use const LOG_CRIT;
use const LOG_DEBUG;
use const LOG_EMERG;
use const LOG_ERR;
use const LOG_INFO;
use const LOG_NOTICE;
use const LOG_WARNING;

class LoggerFactory
{
    /**
     * @var array
     */
    private static array $logLevels = [
        LOG_DEBUG   => Logger::DEBUG,
        LOG_INFO    => Logger::INFO,
        LOG_NOTICE  => Logger::NOTICE,
        LOG_WARNING => Logger::WARNING,
        LOG_ERR     => Logger::ERROR,
        LOG_CRIT    => Logger::CRITICAL,
        LOG_ALERT   => Logger::ALERT,
        LOG_EMERG   => Logger::EMERGENCY,
    ];

    /**
     * @var LoggerInterface[]
     */
    private static array $loggers = [];

    /**
     * @param int $level
     *
     * @return int
     */
    public static function convertPHPLogLevelToMonolog(int $level): int
    {
        return self::$logLevels[$level] ?? $level;
    }

    /**
     * @param array $loggerConfig
     *
     * @return LoggerInterface
     */
    public static function getLogger(array $loggerConfig): LoggerInterface
    {
        $name = $loggerConfig['name'];

        if (!isset(self::$loggers[$name])) {
            self::$loggers[$name] = self::createLogger($loggerConfig);
        }

        return self::$loggers[$name];
    }

    /**
     * @param array $loggerConfig
     *
     * @return LoggerInterface
     */
    private static function createLogger(array $loggerConfig): LoggerInterface
    {
        $container = Container::getContainer();

        /** @var Logger|null $logger */
        $logger = $container ? $container->get('xcart.logger') : null;

        return $logger ? $logger->withName($loggerConfig['name']) : new class extends AbstractLogger {
            public function log($level, $message, array $context = []): void
            {
                throw new \Exception($message);
            }
        };
    }
}
