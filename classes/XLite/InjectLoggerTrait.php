<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite;

use Exception;
use Psr\Log\LoggerInterface;

trait InjectLoggerTrait
{
    /**
     * @param string   $name
     * @param int|null $level
     *
     * @return LoggerInterface
     */
    protected static function getStaticLogger(string $name = 'xlite', ?int $level = null): LoggerInterface
    {
        return Logger::getLogger($name, $level);
    }

    /**
     * @param string   $name
     * @param int|null $level
     *
     * @return LoggerInterface
     */
    protected function getLogger(string $name = 'xlite', ?int $level = null): LoggerInterface
    {
        return static::getStaticLogger($name, $level);
    }

    /**
     * @param int    $level
     * @param string $message
     * @param array  $context
     */
    protected function logPostponed(int $level = 0, string $message = '', $context = []): void
    {
        Logger::getInstance()->logPostponed($message, $level, null, $context);
    }

    /**
     * @param Exception $exception
     */
    protected function logException(Exception $exception): void
    {
        Logger::getInstance()->registerException($exception);
    }
}
