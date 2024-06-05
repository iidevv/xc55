<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite;

use Includes\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class Logger extends \XLite\Base\Singleton
{
    /**
     * Log file name regexp pattern
     */
    public const LOG_FILE_NAME_PATTERN = '#^([0-9]{4}/[0-9]{2}/)?[\w\-]+\.\d{4}-\d{2}-\d{2}\.log$#Ss';

    /**
     * Hash errors
     *
     * @var array
     */
    protected static $hashErrors = [];

    /**
     * @var int
     */
    protected static $level;

    /**
     * Errors translate table (PHP -> PEAR)
     *
     * @var array
     */
    protected $errorsTranslate;

    /**
     * PHP error names
     *
     * @var array
     */
    protected $errorTypes;

    /**
     * Postponed logs
     *
     * @var array
     */
    protected $postponedLogs = [];

    public function __construct()
    {
        set_error_handler([$this, 'errorHandler']);

        // Default log path
        $path = static::generateLogFilePath('php_errors');

        if (!file_exists(dirname($path)) && is_writable(LC_DIR_VAR)) {
            \Includes\Utils\FileManager::mkdirRecursive(dirname($path));
        }

        ini_set('error_log', $path);

        $options = \Includes\Utils\ConfigParser::getOptions(['log_details']);

        if (isset($options['suppress_errors']) && $options['suppress_errors']) {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
        } else {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        }

        if (isset($options['suppress_log_errors']) && $options['suppress_log_errors']) {
            ini_set('log_errors', 0);
        } else {
            ini_set('log_errors', 1);
        }
    }

    /**
     * @param string $name
     *
     * @return LoggerInterface
     */
    public static function getLogger(string $name): LoggerInterface
    {
        return LoggerFactory::getLogger(['name' => $name]);
    }

    /**
     * Get URL to access log file for viewing or downloading (by type)
     *
     * @param string $type
     * @param string $mode 'view', 'download'
     * @param bool   $full
     *
     * @return string
     */
    public static function getLogAccessURL(string $type, string $mode = 'view', bool $full = false): string
    {
        return static::getLogPathAccessURL(static::generateLogFilePath($type), $mode, $full);
    }

    /**
     * Get URL to access log file for viewing or downloading (by path)
     *
     * @param string $path
     * @param string $mode 'view', 'download'
     * @param bool   $full
     *
     * @return string
     */
    public static function getLogPathAccessURL(string $path, string $mode = 'view', bool $full = false): string
    {
        $url = \XLite\Core\Converter::buildURL(
            'log',
            $mode,
            [
                'log' => str_replace(LC_DIR_LOG, '', $path),
            ]
        );

        return $full
            ? \XLite::getInstance()->getShopURL($url)
            : $url;
    }

    public static function generateLogFilePath(string $type): string
    {
        return LC_DIR_LOG . date('Y/m') . LC_DS . static::generateLogFileName($type);
    }

    public static function generateLogFileName(string $type): string
    {
        return $type . '.' . date('Y-m-d') . '.log';
    }

    /**
     * @param string $message Message
     * @param int    $level   Level code
     * @param array  $trace   Back trace
     *
     * @deprecated use \XLite\Logger::getLogger()->log instead
     */
    public function log(string $message, int $level = LOG_DEBUG, $trace = []): void
    {
        static::getLogger('xlite')->log(LoggerFactory::convertPHPLogLevelToMonolog($level), $message, ['trace' => $trace]);
    }

    /**
     * Add postponed log record
     *
     * @param string $message Message
     * @param int    $level   Level code
     * @param null   $trace   Back trace
     * @param array  $context
     */
    public function logPostponed(string $message, int $level = LOG_DEBUG, $trace = null, array $context = []): void
    {
        $this->postponedLogs[] = [
            $message,
            $level,
            $trace ?: debug_backtrace(false),
            $context,
        ];
    }

    /**
     * Write postponed logs
     */
    public function executePostponedLogs(): void
    {
        $logger = static::getLogger('xlite');

        foreach ($this->postponedLogs as [$message, $level, $trace, $context]) {
            $context['trace'] = $context['trace'] ?? $trace;
            $logger->log(LoggerFactory::convertPHPLogLevelToMonolog($level), $message, $context);
        }

        $this->postponedLogs = [];
    }

    /**
     * @param int    $errno   Error code
     * @param string $errstr  Error message
     * @param string $errfile File path
     * @param int    $errline Line number
     *
     * @return bool
     */
    public function errorHandler(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $hash = $errno . ':' . $errfile . ':' . $errline;

        if (
            ini_get('error_reporting') & $errno
            && error_reporting() !== 0
            && (!isset(self::$hashErrors[$hash]) || (int) ini_get('ignore_repeated_errors') !== 1)
            && ((int) ini_get('display_errors') !== 0 || (int) ini_get('log_errors') !== 0)
        ) {
            $errortype = $this->convertErrorLevelToString($errno);

            $message = $errortype . ': ' . $errstr . ' in ' . $errfile . ' on line ' . $errline;

            // Display error
            if ((int) ini_get('display_errors') !== 0) {
                $displayMessage = $message;
                if (isset($_SERVER['REQUEST_METHOD'])) {
                    $displayMessage = '<strong>' . $errortype . '</strong>: ' . $errstr
                        . ' in <strong>' . $errfile . '</strong> on line <strong>' . $errline . '</strong><br />';
                }

                echo($displayMessage . PHP_EOL);
            }

            // Save to log
            if ((int) ini_get('log_errors') !== 0) {
                $logger = static::getLogger('xlite');
                $logger->log($this->convertErrorLevelToLogLevel($errno), $message);
            }

            // Save to cache
            if ((int) ini_get('ignore_repeated_errors') === 1) {
                self::$hashErrors[$hash] = true;
            }
        }

        return true;
    }

    /**
     * Register exception in log file and/or in output
     *
     * @param \Throwable $exception Exception
     */
    public function registerException(\Throwable $exception): void
    {
        if (
            ini_get('error_reporting') & E_ERROR
            && error_reporting() !== 0
            && ((int) ini_get('display_errors') !== 0 || (int) ini_get('log_errors') !== 0)
        ) {
            $message = 'Exception: ' . $exception->getMessage()
                . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine();

            // Display error
            if ((int) ini_get('display_errors') !== 0) {
                $displayMessage = $message;
                if (isset($_SERVER['REQUEST_METHOD'])) {
                    $displayMessage = '<strong>Exception</strong>: ' . $exception->getMessage()
                        . ' in <strong>' . $exception->getFile() . '</strong>'
                        . ' on line <strong>' . $exception->getLine() . '</strong><br />';
                }

                echo($displayMessage . PHP_EOL);
            }

            // Save to log
            if ((int) ini_get('log_errors') !== 0) {
                $logger = static::getLogger('xlite');

                $logger->log(\Monolog\Logger::ERROR, $message, ['trace' => $exception->getTrace()]);
            }
        }
    }

    /**
     * Convert PHP error code to PEAR error code
     *
     * @param integer $errno PHP error code
     *
     * @return integer
     */
    protected function convertErrorLevelToLogLevel(int $errno): int
    {
        if (!isset($this->errorsTranslate)) {
            $this->errorsTranslate = [
                E_ERROR             => \Monolog\Logger::ERROR,
                E_WARNING           => \Monolog\Logger::WARNING,
                E_PARSE             => \Monolog\Logger::CRITICAL,
                E_NOTICE            => \Monolog\Logger::NOTICE,
                E_CORE_ERROR        => \Monolog\Logger::ERROR,
                E_CORE_WARNING      => \Monolog\Logger::WARNING,
                E_COMPILE_ERROR     => \Monolog\Logger::ERROR,
                E_COMPILE_WARNING   => \Monolog\Logger::WARNING,
                E_USER_ERROR        => \Monolog\Logger::ERROR,
                E_USER_WARNING      => \Monolog\Logger::WARNING,
                E_USER_NOTICE       => \Monolog\Logger::NOTICE,
                E_STRICT            => \Monolog\Logger::NOTICE,
                E_RECOVERABLE_ERROR => \Monolog\Logger::ERROR,
            ];

            if (defined('E_DEPRECATED')) {
                $this->errorsTranslate[E_DEPRECATED]      = \Monolog\Logger::WARNING;
                $this->errorsTranslate[E_USER_DEPRECATED] = \Monolog\Logger::WARNING;
            }
        }

        return $this->errorsTranslate[$errno] ?? LOG_INFO;
    }

    /**
     * Get PHP error name
     *
     * @param integer $errno PHP error code
     *
     * @return string
     */
    protected function convertErrorLevelToString(int $errno): string
    {
        if ($this->errorTypes === null) {
            $this->errorTypes = [
                E_ERROR             => 'Error',
                E_WARNING           => 'Warning',
                E_PARSE             => 'Parsing Error',
                E_NOTICE            => 'Notice',
                E_CORE_ERROR        => 'Error',
                E_CORE_WARNING      => 'Warning',
                E_COMPILE_ERROR     => 'Error',
                E_COMPILE_WARNING   => 'Warning',
                E_USER_ERROR        => 'Error',
                E_USER_WARNING      => 'Warning',
                E_USER_NOTICE       => 'Notice',
                E_STRICT            => 'Runtime Notice',
                E_RECOVERABLE_ERROR => 'Catchable fatal error',
            ];

            if (defined('E_DEPRECATED')) {
                $this->errorTypes[E_DEPRECATED]      = 'Warning (deprecated)';
                $this->errorTypes[E_USER_DEPRECATED] = 'Warning (deprecated)';
            }
        }

        return $this->errorTypes[$errno] ?? 'Unknown Error';
    }
}
