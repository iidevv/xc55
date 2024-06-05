<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Monolog\Formatter;

use JsonException;
use Monolog\Formatter\LineFormatter;

class XCartFormatter extends LineFormatter
{
    public function __construct()
    {
        parent::__construct("[%datetime%] %channel%.%level_name%: %message%\n");
    }

    public function format(array $record): string
    {
        $output = parent::format($record);

        if ($record['context']) {
            $output .= 'Context:' . PHP_EOL;
            $output .= $this->convertToString($record['context']) . PHP_EOL;
        }

        $trace = [];
        if ($record['extra']) {
            $trace = $record['extra']['trace'] ?? [];
            unset($record['extra']['trace']);

            $output .= 'Extra:' . PHP_EOL;
            $output .= $this->convertToString($record['extra']) . PHP_EOL;
        }

        if ($trace) {
            $output .= 'Backtrace:' . PHP_EOL;
            foreach ($trace as $line) {
                $output .= $line . PHP_EOL;
            }
        }

        return $output;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    protected function convertToString($data): string
    {
        if ($data === null || is_scalar($data)) {
            return (string) $data;
        }

        try {
            return json_encode($this->normalize($data), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (JsonException $exception) {
            return "Unable to convert to JSON ({$exception->getMessage()})";
        }
    }
}
