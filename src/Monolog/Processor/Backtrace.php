<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Monolog\Processor;

use Closure;
use Monolog\Logger;
use Monolog\Processor\ProcessorInterface;

class Backtrace implements ProcessorInterface
{
    /**
     * @var array
     */
    private $sourceAliases;

    /**
     * @var int
     */
    private $level;

    public function __construct(array $sourceAliases = [], int $level = Logger::DEBUG)
    {
        $this->sourceAliases = $sourceAliases;
        $this->level         = $level;
    }

    public function __invoke(array $record): array
    {
        if (
            !in_array($record['channel'], ['event', 'doctrine', 'request'], true)
            && ($this->level <= Logger::DEBUG
                || $record['level'] >= Logger::ERROR)
        ) {
            $trace = $record['context']['trace'] ?? true;
            if ($trace === true) {
                $trace = debug_backtrace();

                array_splice($trace, 0, 2);
            }

            $record['extra']['trace'] = $this->prepareBackTrace($trace);
        }

        unset($record['context']['trace']);

        return $record;
    }

    public function prepareBackTrace(array $backTrace): array
    {
        $result = [];

        foreach ($backTrace as $line) {
            $parts = [];

            if (isset($line['file'])) {
                $parts[] = 'file ' . $this->prepareFilePath($line['file']);
            } elseif (isset($line['class'], $line['function'])) {
                $parts[] = 'method ' . $line['class'] . '::' . $line['function'] . $this->prepareArguments($line['args'] ?? []);
            } elseif (isset($line['function'])) {
                $parts[] = 'function ' . $line['function'] . $this->prepareArguments($line['args'] ?? []);
            }

            if (isset($line['line'])) {
                $parts[] = $line['line'];
            }

            if ($parts) {
                $result[] = implode(' : ', $parts);
            }
        }

        return $result;
    }

    private function prepareFilePath($file): string
    {
        return str_replace(array_keys($this->sourceAliases), array_values($this->sourceAliases), $file);
    }

    private function prepareArguments(array $arguments): string
    {
        $hideStringValues = $this->level > Logger::DEBUG;

        $result = [];

        foreach ($arguments as $argument) {
            if (is_bool($argument)) {
                $result[] = $argument ? 'true' : 'false';
            } elseif (is_int($argument) || is_float($argument)) {
                $result[] = $argument;
            } elseif (is_string($argument)) {
                if (is_callable($argument)) {
                    $result[] = 'lambda function';
                } else {
                    $result[] = $hideStringValues ? '****' : '\'' . $argument . '\'';
                }
            } elseif (is_resource($argument)) {
                $result[] = (string) $argument;
            } elseif (is_array($argument)) {
                if (is_callable($argument)) {
                    $result[] = 'callback ' . get_class($argument[0]) . '::' . $argument[1];
                } else {
                    $result[] = 'array(' . count($argument) . ')';
                }
            } elseif (is_object($argument)) {
                if (
                    is_callable($argument)
                    && $argument instanceof Closure
                ) {
                    $result[] = 'anonymous function';
                } else {
                    $result[] = 'object of ' . get_class($argument);
                }
            } elseif ($argument === null) {
                $result[] = 'null';
            } else {
                $result[] = 'variable of ' . gettype($argument);
            }
        }

        return '(' . implode(', ', $result) . ')';
    }
}
